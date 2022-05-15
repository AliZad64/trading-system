<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
          'item_destination' => new ItemResource($this->item_destination),
          'item_exchange' => new ItemResource($this->item_exchange),
          'confirmation' => new UserResource($this->item_destination->user),
          'status' => $this->status,
        ];
    }
}
