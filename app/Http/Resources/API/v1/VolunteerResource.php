<?php

namespace App\Http\Resources\API\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $image = resolve('App\Helpers\Image');
        $id = $this->id;
        return [
            'id' => $this->id,
            'station_id' => $this->station->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'image' => $image->getByApi('volunteers/'. $id ),
            'station_title' => isset($this->station) ? $this->station->title : '',
            'created_at' => timeFromNow($this->created_at),
            'api-token' => $this->api_token,
            'is_active' => isset($this->deleted_at) ? false : true
        ];
    }
}
