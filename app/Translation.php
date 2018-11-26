<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        "code", "value", "binding"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function binding()
    {
        return $this->hasOne("App\TranslationBinding");
    }
}
