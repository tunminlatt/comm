<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgrammeDatatableResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'duration' => $this->duration,
            'station_title' => isset($this->station) ? $this->station->title : '',
            'state_title' => $this->state->title,
            'created_at' => $this->created_at->toDateTimeString() . 'MMT',
            'released_at' => $this->schedule != null ? $this->schedule . 'MMT': '',
            'action' => view('admin.programmes.action', ['programme' => $this])->render()
        ];
    }
}