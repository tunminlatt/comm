<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AndriodVersionDatatableResource extends JsonResource
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
            'latest_version_code ' => $this->latest_version_code ,
            'require_force_update' => $this->require_force_update,
            'min_version_code' => $this->min_version_code,
            'play_store_link' => $this->play_store_link,
            'created_at' => $this->created_at->toDateTimeString(),
            'action' => view('admin.andriodVersions.action', ['andriodVersion' => $this])->render()
        ];
    }
}
