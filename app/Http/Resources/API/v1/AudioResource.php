<?php

namespace App\Http\Resources\API\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class AudioResource extends JsonResource
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

        $value = trim($this->duration);

        list($minutes, $seconds) = explode(':', $value);

        $milliseconds = $seconds * 1000 + $minutes * 60000;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'duration' => $milliseconds,
            'created_at' => timeFromNow($this->created_at),
            'image' => $image->getByApi('audios/'. $id .'/banner'),
            'audio' => $image->getByApi('audios/'. $id .'/recording'),
            'note' => $this->note,
            'station_title' => isset($this->station->title) ? $this->station->title : 'Station Deactivate',
            'is_active' => isset($this->deleted_at) ? false : true
        ];
    }
}
