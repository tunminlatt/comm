<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ContentDatatableResource extends JsonResource
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

        $content = $image->getByApi('contents/'. $id .'/content');
        $meta = $image->getByApi('contents/'. $id .'/meta');

        return [
            'id' => $this->id,
            'title' => Str::limit($this->title, 30, ' (...)'),
            'station_title' => isset($this->station) ? $this->station->title : 'N/A',
            'journalist' => isset($this->volunteer) ? $this->volunteer->name : $this->name,
            'description' => isset($this->description) ? Str::limit($this->description, 60, ' (...)') : '',
            'created_at' => $this->created_at->toDateTimeString(),
            'action' => view('admin.contents.action', ['content' => $this])->render()
        ];

    }
}
