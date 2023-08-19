<?php

namespace App\Repositories;

use App\Models\Volunteer;

class VolunteerRepository
{
    public function __construct(Volunteer $volunteer) {
        $this->volunteer = $volunteer;
    }

    public function getAllForTable($request, $eagerLoad = [], $withTrash = true, $onlyTrash = false) {
        // prepare variables
        $user = $request->user();
        $userStationID = $user->station_id;
        $userTypeID = $user->user_type_id;

        return $this->volunteer
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($onlyTrash, function ($query) {
                        return $query->onlyTrashed();
                    })
                    ->when($userTypeID == 2, function ($query) use ($userStationID) {
                        return $query->where('station_id', $userStationID);
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
        return $this->volunteer
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
                    ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                        return $query->paginate($paginateCount);
                    }, function ($query) {
                        return $query->get();
                    });
    }

    public function show($column, $value, $eagerLoad = [], $withTrash = false, $returnMany = false) {
        return $this->volunteer
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
        return $this->volunteer->create($payload);
    }

    public function update($id, $payload, $withTrash = false) {
        return $this->volunteer
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->find($id)->update($payload);
    }

    public function destroy($id, $type = 1) { // 1 - normal, 2 - force
        if ($type == 1) {
            return $this->volunteer->destroy($id);
        } else {
            return $this->volunteer->withTrashed()->find($id)->forceDelete();
        }
    }

    public function restore($id) {
        return $this->volunteer->withTrashed()->find($id)->restore();
    }

    public function share($eagerLoad = [], $withTrash = true) {
        return $this->volunteer
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad) {
                        return $query->whereIN('id', $eagerLoad);
                    })->get();
    }

    public function getByStaion($value, $eagerLoad = [], $withTrash = true)
    {
        return $this->volunteer
            ->when($withTrash, function ($query) {
                return $query->withTrashed();
            })
            ->where('station_id', $value)
            ->when($eagerLoad, function ($query) use ($eagerLoad) {
                return $query->whereIN('id', $eagerLoad);
            })->get();
    }
}