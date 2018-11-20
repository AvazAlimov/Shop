<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function names()
    {
        //TODO test: Does HasOne object return property translations
        return $this->hasOne("App\TranslationBinding")->translations();
    }
}
