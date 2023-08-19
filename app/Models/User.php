<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GlobalScopes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes, GlobalScopes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = toUnicode($value);
    }

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class)->withTrashed();
    }

    public function approvedProgrammes()
    {
        return $this->hasMany(Programme::class, 'approved_by')->withTrashed();
    }

    public function uploadedProgrammes()
    {
        return $this->hasMany(Programme::class, 'uploaded_by')->withTrashed();
    }

    public function audios()
    {
        return $this->hasMany(Audio::class)->withTrashed();
    }
}
