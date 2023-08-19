<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class StationManagerDatatableResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'station_title' => isset($this->station) ? $this->station->title : '',
            'created_at' => $this->created_at->toDateTimeString(),
            'action' => view('admin.stationManagers.action', ['user' => $this])->render()
        ];
    }
}
