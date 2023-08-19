<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ShareDatatableResource extends JsonResource
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
            'duration' => $this->duration,
            'station_title' => isset($this->station) ? $this->station->title : '',
            'volunteer_name' => isset($this->volunteer) ? $this->volunteer->name : '',
            'created_at' => $this->created_at->toDateTimeString(),
            'action' => view('admin.shares.action', ['audio' => $this])->render()
        ];
    }
}
