<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AudioDatatableResource extends JsonResource
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
        $audio = $image->getByApi('audios/'. $id .'/recording');
        $banner = $image->getByApi('audios/'. $id .'/banner');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'duration' => $this->duration,
            'station_title' => isset($this->station) ? $this->station->title : '',
            'volunteer_name' => isset($this->volunteer) ? $this->volunteer->name : \App\Models\Volunteer::find($this->uploaded_by)->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'action' => view('admin.audios.action', ['audio' => $this, 'recording' => $audio, 'image' => $banner])->render()
        ];
    }
}
