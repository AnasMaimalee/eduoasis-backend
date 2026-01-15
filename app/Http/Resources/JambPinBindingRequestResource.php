<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JambPinBindingRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'profile_code' => $this->profile_code,
            'email' => $this->email,

            'result_file' => $this->result_file
                ? asset('storage/' . $this->result_file)
                : null,

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

