<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentDatatableResource extends JsonResource
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
        $file = $image->getByApi('documents/'. $id .'/file');
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'station_title' => isset($this->station) ? $this->station->title : 'Station Deactivate',
            'volunteer_name' => isset($this->volunteer) ? $this->volunteer->name : \App\Models\Volunteer::find($this->uploaded_by)->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'action' => view('admin.documents.action', ['document' => $this, 'file' => $file, 'extension' => $extension])->render()
        ];
    }
}
