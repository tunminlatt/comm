<?php

namespace App\Http\Resources\API\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'about_message' => $this->description,
        ];
    }
}
