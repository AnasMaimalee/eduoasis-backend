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
            'email' => $this->email,
            'status' => $this->status,

            'customer_price' => $this->customer_price,
            'admin_payout' => $this->admin_payout,
            'platform_profit' => $this->platform_profit,

            'is_paid' => $this->is_paid,
            'created_at' => $this->created_at,
        ];
    }

}
