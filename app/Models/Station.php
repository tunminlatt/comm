<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GlobalScopes;
use App\Traits\UUID;

class Station extends Model
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

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = toUnicode($value);
    }

    public function stationManagers()
    {
        return $this->hasMany(User::class)->withTrashed();
    }

    public function volunteers()
    {
        return $this->hasMany(Volunteer::class)->withTrashed();
    }

    public function audios()
    {
        return $this->hasMany(Audio::class)->withTrashed();
    }

    public function programmes()
    {
        return $this->hasMany(Programme::class)->withTrashed();
    }

     public function documents()
    {
        return $this->hasMany(Document::class)->withTrashed();
    }
}
