<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email_primary', 255)->nullable();
            $table->string('email_secondary', 255)->nullable();
            $table->string('phone_primary', 50)->nullable();
            $table->string('phone_secondary', 50)->nullable();
            $table->string('company', 255)->nullable();
            $table->timestamps();

            // Unique email per user (allows same email across different users)
            $table->unique(['user_id', 'email_primary']);

            // Index for faster lookups
            $table->index(['user_id', 'last_name', 'first_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
