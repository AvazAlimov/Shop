<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        "code"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function translations()
    {
        return $this->hasMany("App\Translation");
    }
}
