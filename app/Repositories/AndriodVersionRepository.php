<?php

namespace App\Repositories;

use App\Models\AndriodVersion;

class AndriodVersionRepository
{
    public function __construct(AndriodVersion $andriodversion) {
        $this->andriodversion = $andriodversion;
    }

    public function all($eagerLoad = [], $withTrash = true, $paginateCount = 0) {
        return $this->andriodversion
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->withEagerTrashed($eagerLoad);
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
        return $this->andriodversion
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->when($eagerLoad, function ($query) use ($eagerLoad, $withTrash) {
                        if ($withTrash) {
                            return $query->withEagerTrashed($eagerLoad);
                        } else {
                            return $query->with($eagerLoad);
                        }
                    })
                    ->where($column, $value)
                    ->first();
    }

    public function create($payload) {
        return $this->andriodversion->create($payload);
    }

    public function update($id, $payload, $withTrash = false) {
        return $this->andriodversion
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->find($id)->update($payload);
    }

    public function canDestroy($id) {
        return $this->andriodversion->where('id', $id)->doesntHave('childrens')->exists();
    }

    public function destroy($id, $type = 1) { // 1 - normal, 2 - force
        if ($type == 1) {
            return $this->andriodversion->destroy($id);
        } else {
            return $this->andriodversion->withTrashed()->find($id)->forceDelete();
        }
    }

    public function restore($id) {
        return $this->andriodversion->withTrashed()->find($id)->restore();
    }
}