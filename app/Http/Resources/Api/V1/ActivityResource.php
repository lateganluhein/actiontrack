<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logic' => $this->logic,
            'next_step' => $this->next_step,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'status' => $this->status,
            'status_label' => $this->status_label, // Accessor
            'days_until_due' => $this->days_until_due, // Accessor
            'is_overdue' => $this->is_overdue, // Accessor
            'urgency_level' => $this->urgency_level, // Accessor
            // Note: urgency_badge accessor returns HTML, so not suitable for API here

            'lead_id' => $this->lead_id,
            'lead' => new PersonResource($this->whenLoaded('lead')),
            'parties' => PersonResource::collection($this->whenLoaded('parties')),

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
