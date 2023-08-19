<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GlobalScopes;
use App\Traits\UUID;

class Audio extends Model
{
    use SoftDeletes, GlobalScopes, UUID;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public $table = 'audios';

    public $incrementing = false;

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = toUnicode($value);
    }

    public function setNoteAttribute($value)
    {
        $this->attributes['note'] = toUnicode($value);
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'uploaded_by')->withTrashed();
    }

    public function station()
    {
        return $this->belongsTo(Station::class)->withTrashed();
    }

    public function stationManagers()
    {
        return $this->belongsToMany(User::class)->withTrashed();
    }
}
