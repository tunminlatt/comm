<?php

namespace App\Repositories;

use App\Models\Audio;

class ShareRepository
{
    public function __construct(Audio $audio) {
        $this->audio = $audio;
    }

    public function getAllForTable($request, $status = 'private', $withTrash = false) {
        // prepare variables
        $user = $request->user();
        $userID = $user->id;
        $userStationID = $user->station_id;
        $userTypeID = $user->user_type_id;

        $title = $request->title;
        $volunteerID = $request->volunteerID;
        $uploadedDate = $request->uploadedDate;
        $stationID = $request->stationID;

        return $this->audio
                ->when($withTrash, function ($query) {
                    return $query->withTrashed();
                })
                ->when($title, function ($query) use ($title) {
                    return $query->where('title', 'like', '%'. $title .'%');
                })
                ->when($volunteerID, function ($query) use ($volunteerID) {
                    return $query->where('uploaded_by', $volunteerID);
                })
                ->when($uploadedDate, function ($query) use ($uploadedDate) {
                    $dates = explode(' to ', $uploadedDate);
                    $startDate = $dates[0];
                    $endDate = $dates[1];

                    return $query->whereBetween('created_at', [$startDate." 00:00:00", $endDate." 23:59:59"]);
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->orderBy('deleted_at', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
    }

    public function show($column, $value, $status = 'private', $withTrash = false ,$returnMany = false) {
        return $this->audio
                ->when($withTrash, function ($query) {
                    return $query->withTrashed();
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->where($column, $value)
                ->when($returnMany, function ($query, $role) {
                    return $query->get();
                }, function ($query) {
                    return $query->first();
                });
    }

    public function volunteer($status = 'private', $withTrash = false) {
        return $this->audio
        ->when($withTrash, function ($query) {
            return $query->withTrashed();
        })
        ->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->pluck('uploaded_by')->toArray();
    }

    public function station($status = 'private', $withTrash = false) {
        return $this->audio
        ->when($withTrash, function ($query) {
            return $query->withTrashed();
        })
        ->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->pluck('station_id')->toArray();
    }

}