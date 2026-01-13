<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JambResultRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'profile_code' => $this->profile_code,
            'email' => $this->email,
            'registration_number' => $this->registration_number,

            'result_file' => $this->result_file
                ? asset('storage/' . $this->result_file)
                : null,

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

}
