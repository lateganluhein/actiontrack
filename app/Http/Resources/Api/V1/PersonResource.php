<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name, // Accessor
            'email_primary' => $this->email_primary,
            'email_secondary' => $this->email_secondary,
            'phone_primary' => $this->phone_primary,
            'phone_secondary' => $this->phone_secondary,
            'company' => $this->company,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
