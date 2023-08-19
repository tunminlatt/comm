<?php

namespace App\Models;

use App\Traits\UUID;
use App\Models\Content;
use App\Traits\GlobalScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Volunteer extends Model
{
    use SoftDeletes, GlobalScopes, UUID;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public $incrementing = false;

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = toUnicode($value);
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = toUnicode($value);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function audios()
    {
        return $this->hasMany(Audio::class, 'uploaded_by')->withTrashed();
    }

    public function station()
    {
        return $this->belongsTo(Station::class)->withTrashed();
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by')->withTrashed();
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'uploaded_by')->withTrashed();
    }
}
