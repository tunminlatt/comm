<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GlobalScopes;

class Language extends Model
{
    use SoftDeletes, GlobalScopes;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function volunteers()
    {
        return $this->hasMany(Volunteer::class);
    }
}
