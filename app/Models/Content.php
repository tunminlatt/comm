<?php

namespace App\Models;

use App\Traits\UUID;
use App\Models\Volunteer;
use App\Traits\GlobalScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes, GlobalScopes, UUID;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public $table = 'contents';

    public $incrementing = false;

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = toUnicode($value);
    }

    public function station()
    {
        return $this->belongsTo(Station::class)->withTrashed();
    }

    // public function stationManagers()
    // {
    //     return $this->belongsToMany(User::class)->withTrashed();
    // }
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'uploaded_by')->withTrashed();
    }
}
