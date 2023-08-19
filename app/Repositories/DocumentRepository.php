<?php

namespace App\Repositories;

use App\Models\Document;

class DocumentRepository
{
    public function __construct(Document $document) {
        $this->document = $document;
    }

    public function getAllForTable($request, $eagerLoad = [], $withTrash = true, $onlyTrash = false) {
        // prepare variables
        $user = $request->user();
        $userID = $user->id;
        $userStationID = $user->station_id;
        $userTypeID = $user->user_type_id;

        $title = $request->title;
        $volunteerID = $request->volunteerID;
        $uploadedDate = $request->uploadedDate;
        $stationID = $request->stationID;

        return $this->document
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($onlyTrash, function ($query) {
                        return $query->onlyTrashed();
                    })
                    ->when($userTypeID == 2, function ($query) use ($userID, $userStationID) {
                        return $query->where('station_id', $userStationID);
                                    // ->orWhereHas('users', function ($query) use ($userID) {
                                    //     $query->where('user_id', $userID);
                                    // });
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
                    ->when($stationID, function ($query) use ($stationID) {
                        return $query->where('station_id', $stationID);
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->with($eagerLoad);
                        } else {
                            return $query->with($eagerLoad);
                        }
                    })
                    ->orderBy('deleted_at', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function all($eagerLoad = [], $withTrash = true, $paginateCount = 0) {
        return $this->document
                    // ->when($withTrash, function ($query) {
                    //     return $query->withTrashed();
                    // })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->with($eagerLoad);
                        } else {
                            return $query->with($eagerLoad);
                        }
                    })
                    ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                        return $query->paginate($paginateCount);
                    }, function ($query) {
                        return $query->get();
                    });
    }

    public function show($column, $value, $eagerLoad = [], $withTrash = false, $returnMany = false, $latest = false) {
        return $this->document
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->with($eagerLoad);
                        } else {
                            return $query->with($eagerLoad);
                        }
                    })
                    ->where($column, $value)
                    ->when($latest, function ($query) {
                        return $query->orderBy('created_at', 'desc');
                    })
                    ->when($returnMany, function ($query, $role) {
                        return $query->get();
                    }, function ($query) {
                        return $query->first();
                    });
    }

    public function create($payload) {
        return $this->document->create($payload);
    }

    public function update($id, $payload, $withTrash = false) {
        return $this->document
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->find($id)->update($payload);
    }

    public function destroy($id, $type = 1) { // 1 - normal, 2 - force
        if ($type == 1) {
            return $this->document->destroy($id);
        } else {
            return $this->document->withTrashed()->find($id)->forceDelete();
        }
    }

    public function restore($id) {
        return $this->document->withTrashed()->find($id)->restore();
    }

    public function deleteByAudioID ($id) {
        return $this->document->withTrashed()->find($id)->stationManagers()->detach();
    }

    public function createByAudioID ($id, $payload) {
        return $this->document->withTrashed()->find($id)->stationManagers()->attach($payload);
    }
}