<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed photo
 * @property mixed photoBindings
 * @property mixed translationBindings
 */
class Category extends Model
{
    protected $fillable = [
        "default", "name", "parent", "photo"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function products()
    {
        return $this->hasMany("App\Product");
    }

    public function photoBindings()
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

    public function parentCategory()
    {
        return $this->hasOne("App\Category", "id", "parent");
    }
}
