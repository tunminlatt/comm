<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class StationDatatableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at->toDateTimeString(),
            'action' => view('admin.stations.action', ['station' => $this])->render()
        ];
    }
}
