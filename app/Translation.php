<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    public function binding()
    {
        return $this->hasOne("App\TranslationBinding");
    }
}
