<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    public function binding()
    {
        return $this->hasOne("App\PhotoBinding");
    }
}
