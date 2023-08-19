<?php

namespace App\Http\Resources\API\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'title' => $this->title,
            'note' => $this->note,
            'file' => $image->getByApi('documents/'. $id .'/file'),
            'created_at' => timeFromNow($this->created_at),
            'station_title' => isset($this->station->title) ? $this->station->title : 'Station Deactivate',
            'is_active' => isset($this->deleted_at) ? false : true
        ];
    }
}
