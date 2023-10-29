<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'datetime' => $this->datetime,
            'datetime_string' => date('Y-m-d H:i', $this->datetime),
            'factory_id' => $this->factory_id,
            'reserve' => $this->reserve
        ];
    }
}
