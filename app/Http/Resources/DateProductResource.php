<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DateProductResource extends JsonResource
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
            'name' => $this->productName(),
            'done' => (bool)$this->done,
            'end' => $this->end->format('d.m.Y'),
            'expDays' => $this->days,
            'markdown' => (bool)$this->markdown,
            'user' => $this->user->name,
        ];
    }
}
