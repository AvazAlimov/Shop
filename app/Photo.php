<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        "filename", "binding"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function binding()
    {
        return $this->hasOne("App\PhotoBinding");
    }
}
