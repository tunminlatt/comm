<?php

namespace App\Repositories;

use App\Models\Station;

class StationRepository
{
    public function __construct(Station $station) {
        $this->station = $station;
    }

    public function getAllForTable($request, $eagerLoad = [], $withTrash = true, $onlyTrash = false) {
        return $this->station
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($onlyTrash, function ($query) {
                        return $query->onlyTrashed();
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
        return $this->station
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
                    ->when(true, function ($query) {
                        return $query->orderBy('created_at', 'desc');
                    })
                    ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                        return $query->paginate($paginateCount);
                    }, function ($query) {
                        return $query->get();
                    });
    }

    public function privateOrPublicStations($privateOrPublic, $eagerLoad = [], $withTrash = true, $paginateCount = 0) {
        return $this->station
                    ->where('is_public', $privateOrPublic)
                    ->withCount('programmes')
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
                    ->when(true, function ($query) {
                        return $query->orderBy('title', 'asc');
                    })
                    ->when($paginateCount, function ($query, $role) use ($paginateCount) {
                        return $query->paginate($paginateCount);
                    }, function ($query) {
                        return $query->get();
                    });
    }

    public function show($column, $value, $eagerLoad = [], $withTrash = false, $returnMany = false) {
        return $this->station
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
        return $this->station->create($payload);
    }

    public function update($id, $payload, $withTrash = false) {
        return $this->station
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->find($id)->update($payload);
    }

    public function destroy($id, $type = 1) { // 1 - normal, 2 - force
        if ($type == 1) {
            return $this->station->destroy($id);
        } else {
            return $this->station->withTrashed()->find($id)->forceDelete();
        }
    }

    public function restore($id) {
        return $this->station->withTrashed()->find($id)->restore();
    }

    public function share($eagerLoad = [], $withTrash = true) {
        return $this->station
            ->when($withTrash, function ($query) {
                return $query->withTrashed();
            })
            ->when($eagerLoad, function ($query) use ($eagerLoad) {
                return $query->whereIN('id', $eagerLoad);
            })->get();
    }
}
