<?php

namespace App\Traits;

use Carbon\Carbon;

trait GlobalScopes
{
    public function scopeWithEagerTrashed($query, $relations)
    {
        $eagerLoads = [];
        foreach ($relations as $key => $relation) {
            $eagerLoads[$relation] = function ($query) { $query->withTrashed(); };
        }
        return $query->with($eagerLoads);
    }

    public function getTimeFromNowAttribute()
    {
        $currentDate = Carbon::now();
        $createdAt = $this->created_at;
        $dayDifference = $currentDate->diffInDays($createdAt);

        return $dayDifference ? $dayDifference .' days ago' : $currentDate->diffInMinutes($createdAt) .' mins ago';
    }
}