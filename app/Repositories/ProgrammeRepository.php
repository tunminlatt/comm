<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Programme;

class ProgrammeRepository
{
    public function __construct(Programme $programme) {
        $this->programme = $programme;
    }

    public function getAllForTable($request, $eagerLoad = [], $withTrash = true, $onlyTrash = false) {
        // prepare variables
        $user = $request->user();
        $userStationID = $user->station_id;
        $userTypeID = $user->user_type_id;

        $title = $request->title;
        $uploadedDate = $request->uploadedDate;
        $stationID = $request->stationID;

        return $this->programme
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($onlyTrash, function ($query) {
                        return $query->onlyTrashed();
                    })
                    ->when($userTypeID == 2, function ($query) use ($userStationID) {
                        return $query->where('station_id', $userStationID);
                    })
                    ->when($title, function ($query) use ($title) {
                        return $query->where('title', 'like', '%'. $title .'%');
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
                    ->where('permanently_delete',0)
                    ->orderBy('deleted_at', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function all($eagerLoad = [], $withTrash = true, $paginateCount = 0) {
        return $this->programme
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

    public function show($column, $value, $eagerLoad = [], $withTrash = false, $returnMany = false) {
        return $this->programme
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
                    ->when($returnMany, function ($query, $role) {
                        return $query->get();
                    }, function ($query) {
                        return $query->first();
                    });
    }

    public function create($payload) {
        return $this->programme->create($payload);
    }

    public function update($id, $payload, $withTrash = false) {
        return $this->programme
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->find($id)->update($payload);
    }

    public function destroy($id, $type = 1) { // 1 - normal, 2 - force
        if ($type == 1) {
            return $this->programme->destroy($id);
        } else {
            $payload['permanently_delete'] = 1;
            return $this->programme
            ->withTrashed()
            ->find($id)->update($payload);
            // return $this->programme->withTrashed()->find($id)->forceDelete();
        }
    }

    public function restore($id) {
        return $this->programme->withTrashed()->find($id)->restore();
    }

    public function getProgramByValue($column, $value, $eagerLoad = [], $withTrash = false, $paginateCount = 0, $latest = false) {
        return $this->programme
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
                return $query->orderBy('schedule', 'desc');
            })
            ->where('state_id', 2)
            ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                return $query->paginate($paginateCount)->appends($_GET);
            }, function ($query) {
                return $query->get();
            });
    }

    public function getLatestPublishedDate($station_id){
        return $this->programme->where('station_id', $station_id)
            ->whereTime('schedule', '<=', Carbon::now())
            ->orderBy('schedule', 'desc')
            ->first();
    }

    public function getProgramByStation($station, $type, $eagerLoad = [], $withTrash = false, $paginateCount = 0, $latest = false) {
        return $this->programme
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
            ->where('station_id', $station)
            ->where('type', $type)
            ->when($latest, function ($query) {
                return $query->orderBy('schedule', 'desc');
            })
            ->where('state_id', 2)
            ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                return $query->paginate($paginateCount)->appends($_GET);
            }, function ($query) {
                return $query->get();
            });
    }

    public function searchProgramme(array $stationIds, $eagerLoad = [], $withTrash = false, $paginateCount = 0, $latest = false) {
        return $this->programme
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
            ->where('description', 'like', '%' . request()->key_word . '%')
            ->when($stationIds, function($query) use($stationIds) {
                $query->whereHas('station', function($q) use($stationIds) {
                    $q->whereIn('id', $stationIds);
                });
            })
            ->when($latest, function ($query) {
                return $query->orderBy('schedule', 'desc');
            })
            ->where('state_id', 2)
            ->whereBetween('schedule', [request()->start_date, request()->end_date])
            ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                return $query->paginate($paginateCount)->appends($_GET);
            }, function ($query) {
                return $query->get();
            });
    }
}
