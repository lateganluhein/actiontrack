<?php

namespace App\Console\Commands;

use App\Mail\AdminDailySummary;
use App\Mail\DailySummary;
use App\Mail\PersonalSummary;
use App\Models\Activity;
use App\Models\Person;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailySummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:send-summaries
                            {--user= : Send to specific user ID only}
                            {--skip-admin : Skip sending master summary to admin}
                            {--skip-people : Skip sending summaries to people/contacts}
                            {--skip-users : Skip sending summaries to users}
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily activity summaries to admin, users, and people involved in activities';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting daily summary email job...');
        $this->newLine();

        $failedCount = 0;

        // Step 1: Send master summary to admin
        if (!$this->option('skip-admin')) {
            $failedCount += $this->sendAdminSummary();
        }

        // Step 2: Send to users with daily summaries enabled
        if (!$this->option('skip-users')) {
            $failedCount += $this->sendUserSummaries();
        }

        // Step 3: Send to all people involved in activities
        if (!$this->option('skip-people')) {
            $failedCount += $this->sendPeopleSummaries();
        }

        $this->newLine();
        $this->info('Daily summary job completed.');

        return $failedCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Send master summary to admin email.
     */
    protected function sendAdminSummary(): int
    {
        $adminEmail = config('app.admin_email');
        if (!$adminEmail) { $this->warn('ADMIN_EMAIL not configured'); return 0; }
        $this->info('Sending statistics to admin...');

        $allActivities = Activity::withoutGlobalScope('user')->get();
        $userStats = [];
        foreach (User::all() as $user) {
            $ua = Activity::withoutGlobalScope('user')->where('user_id', $user->id)->get();
            if ($ua->isNotEmpty()) {
                $userStats[] = ['name' => $user->name, 'total' => $ua->count(),
                    'in_progress' => $ua->where('status', 'in_progress')->count(),
                    'overdue' => $ua->filter(fn($a) => $a->is_overdue)->values()->count(),
                    'completed' => $ua->where('status', 'completed')->count()];
            }
        }
        $stats = ['date' => now()->format('l, d F Y'),
            'users' => ['total' => User::count(), 'active' => User::whereHas('settings', fn($q) => $q->where('daily_summary_enabled', true))->count(),
                'new_today' => User::whereDate('created_at', today())->count(), 'new_this_week' => User::where('created_at', '>=', now()->subDays(7))->count()],
            'activities' => ['total' => $allActivities->count(), 'in_progress' => $allActivities->where('status', 'in_progress')->count(),
                'completed' => $allActivities->where('status', 'completed')->count(), 'cancelled' => $allActivities->where('status', 'cancelled')->count(),
                'overdue' => $allActivities->filter(fn($a) => $a->is_overdue)->values()->count(),
                'due_soon' => $allActivities->filter(fn($a) => !$a->is_overdue && $a->days_until_due !== null && $a->days_until_due >= 0 && $a->days_until_due <= 7)->values()->count()],
            'contacts' => ['total' => Person::withoutGlobalScope('user')->count()], 'per_user' => $userStats];

        if ($this->option('dry-run')) { $this->info("   [DRY RUN] Would send to {$adminEmail}"); return 0; }
        try {
            Mail::to($adminEmail)->send(new AdminDailySummary($stats));
            $this->info("   Statistics sent to {$adminEmail}"); return 0;
        } catch (\Exception $e) { $this->error("   Failed: " . $e->getMessage()); return 1; }
    }

    /**
     * Send summaries to users with the feature enabled.
     */
    protected function sendUserSummaries(): int
    {
        $this->newLine();
        $this->info('👤 Sending summaries to users...');

        $query = User::whereHas('settings', function ($q) {
            $q->where('daily_summary_enabled', true);
        });

        // Filter to specific user if provided
        if ($userId = $this->option('user')) {
            $query->where('id', $userId);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->line('   No users found with daily summaries enabled.');
            return 0;
        }

        $this->line("   Found {$users->count()} user(s) with daily summaries enabled.");

        $successCount = 0;
        $failedCount = 0;

        foreach ($users as $user) {
            // Get user's in-progress activities
            $activities = Activity::withoutGlobalScope('user')
                ->where('user_id', $user->id)
                ->where('status', 'in_progress')
                ->with('lead')
                ->get();

            if ($activities->isEmpty()) {
                $this->line("   - {$user->email}: No active activities, skipping.");
                continue;
            }

            if ($this->option('dry-run')) {
                $this->info("   [DRY RUN] Would send to {$user->email}: {$activities->count()} activities");
                $successCount++;
                continue;
            }

            try {
                Mail::to($user->email)->send(new DailySummary($user, $activities));
                $this->info("   ✓ Sent to {$user->email}: {$activities->count()} activities");
                $successCount++;
            } catch (\Exception $e) {
                $this->error("   ✗ Failed for {$user->email}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->line("   Summary: {$successCount} sent, {$failedCount} failed");

        return $failedCount;
    }

    /**
     * Send summaries to all people involved in activities.
     */
    protected function sendPeopleSummaries(): int
    {
        $this->newLine();
        $this->info('👥 Sending summaries to people (contacts)...');

        // Get all in-progress activities
        $activities = Activity::withoutGlobalScope('user')
            ->where('status', 'in_progress')
            ->with(['lead', 'parties'])
            ->get();

        if ($activities->isEmpty()) {
            $this->line('   No active activities, skipping people notifications.');
            return 0;
        }

        // Build person -> activities map
        $activitiesByPerson = [];

        foreach ($activities as $activity) {
            // Add to lead's list
            if ($activity->lead && $activity->lead->email_primary) {
                $personId = $activity->lead->id;
                if (!isset($activitiesByPerson[$personId])) {
                    $activitiesByPerson[$personId] = [
                        'person' => $activity->lead,
                        'activities' => collect(),
                    ];
                }
                $activitiesByPerson[$personId]['activities']->push($activity);
            }

            // Add to each party's list
            foreach ($activity->parties as $party) {
                if ($party->email_primary) {
                    $personId = $party->id;
                    if (!isset($activitiesByPerson[$personId])) {
                        $activitiesByPerson[$personId] = [
                            'person' => $party,
                            'activities' => collect(),
                        ];
                    }
                    // Avoid duplicates if person is both lead and party
                    if (!$activitiesByPerson[$personId]['activities']->contains('id', $activity->id)) {
                        $activitiesByPerson[$personId]['activities']->push($activity);
                    }
                }
            }
        }

        if (empty($activitiesByPerson)) {
            $this->line('   No people with email addresses to notify.');
            return 0;
        }

        $this->line('   Found ' . count($activitiesByPerson) . ' people to notify.');

        $successCount = 0;
        $failedCount = 0;

        foreach ($activitiesByPerson as $data) {
            $person = $data['person'];
            $personActivities = $data['activities'];

            if ($this->option('dry-run')) {
                $this->info("   [DRY RUN] Would send to {$person->full_name} ({$person->email_primary}): {$personActivities->count()} activities");
                $successCount++;
                continue;
            }

            try {
                Mail::to($person->email_primary)->send(new PersonalSummary($person, $personActivities));
                $this->info("   ✓ Sent to {$person->full_name} ({$person->email_primary}): {$personActivities->count()} activities");
                $successCount++;

                // Brief delay between emails to avoid rate limiting
                usleep(500000); // 0.5 second
            } catch (\Exception $e) {
                $this->error("   ✗ Failed for {$person->email_primary}: {$e->getMessage()}");
                $failedCount++;
            }
        }

        $this->line("   Summary: {$successCount} sent, {$failedCount} failed");

        return $failedCount;
    }
}
