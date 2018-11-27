<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed photoBindings
 * @property mixed translationBindings
 * @property mixed photo
 */
class Season extends Model
{
    protected $fillable = [
        "name", "default", "photo"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function products()
    {
        return $this->hasMany("App\Product");
    }

    protected function photoBindings()
    {
        return $this->hasOne("App\PhotoBinding", "id", "photo");
    }

    public function photoPath()
    {
        if (!$this->photo) {
            return null;
        }
        $path = $this->photoBindings->photos->pluck("filename")->first();
        unset($this->photoBindings);
        return $path;
    }

    public function translationBindings()
    {
        return $this->hasOne("App\TranslationBinding", "id", "name");
    }

    public function nameTranslations()
    {
        $name = $this->translationBindings->translations->pluck("value", "code");
        unset($this->translationBindings);
        return $name;
    }
}
