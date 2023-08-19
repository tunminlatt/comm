<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    protected $fillable = ['programme_id', 'reaction_count'];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }
}
