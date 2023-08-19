<?php

namespace App\Http\Resources\API\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgrammeResource extends JsonResource
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

        if($this->type == 'video' || $this->type == 'audio') {
            $rawDuration = $image->getDuration($image->getByApi('programmes/'. $id . '/' . $this->type, [], false, true));
            $duration = trim($rawDuration);
        } else {
            $duration = null;
        }

        if (strpos($duration, 'sh:') !== false) {
            $duration = null;
        }

        if($this->type == 'photo') {
            $content = implode(',', $image->getByApiMultiple('programmes/'. $id . '/' . $this->type));
        } else {
            $content = $image->getByApi('programmes/'. $id . '/' . $this->type);
        }

        if($this->type == 'video') {
            $thumbnail = asset('thumbnails/'.$id.'.jpeg');
        } else {
            $realThumbnail = $image->getByApi('programmes/'. $id .'/thumbnail');
            if($realThumbnail != null) {
                $thumbnail = $realThumbnail;
            } else {
                $thumbnail = $image->getByApi('stations/'. $this->station->id);
            }
        }

        return [
            'id' => $this->id,
            'station_id' => $this->station->id ?? null,
            'title' => $this->title,
            'created_at' => $this->schedule !== null ? timeFromNow($this->schedule) : timeFromNow($this->created_at),
            'content' => $content,
            'duration' => $duration,
            'thumbnail' => $thumbnail,
            'type' => $this->type,
            'description' => $this->description,
            'station_title' => isset($this->station->title) ? $this->station->title : 'Station Deactivate',
            'station_image' => $image->getByApi('stations/'. $this->station->id)
        ];
    }
}
