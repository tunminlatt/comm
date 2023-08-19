<?php

namespace App\Models;

use App\Traits\UUID;
use App\Models\Reaction;
use App\Traits\GlobalScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programme extends Model
{
    use SoftDeletes, GlobalScopes, UUID;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public $incrementing = false;

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = toUnicode($value);
    }

    public function station()
    {
        return $this->belongsTo(Station::class)->withTrashed();
    }

    public function uploadedUser()
    {
        return $this->belongsTo(User::class, 'uploaded_by')->withTrashed();
    }

    public function approvedUser()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function reaction()
    {
        return $this->hasOne(Reaction::class);
    }
}
