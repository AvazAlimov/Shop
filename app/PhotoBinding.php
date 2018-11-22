<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhotoBinding extends Model
{
    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function photos()
    {
        return $this->hasMany("App\Photo", "binding");
    }
}
