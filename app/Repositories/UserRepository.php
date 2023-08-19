<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct(User $user) {
        $this->user = $user;
    }

    public function getAllForTable($request, $eagerLoad = [], $withTrash = true, $filter = false, $onlyTrash = false) {
        return $this->user
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
                    ->when($filter, function ($query) use ($filter) {
                        return $query->where($filter['column'], $filter['value']);
                    })
                    ->orderBy('deleted_at', 'asc')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public function all($eagerLoad = [], $withTrash = true, $paginateCount = 0) {
        return $this->user
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
        return $this->user
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
        return $this->user->create($payload);
    }

    public function update($id, $payload, $withTrash = false) {
        return $this->user
                    ->when($withTrash, function ($query) {
                        return $query->withTrashed();
                    })
                    ->find($id)->update($payload);
    }

    public function canDestroy($id) {
        return $this->user->where('id', $id)->doesntHave('childrens')->exists();
    }

    public function destroy($id, $type = 1) { // 1 - normal, 2 - force
        if ($type == 1) {
            return $this->user->destroy($id);
        } else {
            return $this->user->withTrashed()->find($id)->forceDelete();
        }
    }

    public function restore($id) {
        return $this->user->withTrashed()->find($id)->restore();
    }
}