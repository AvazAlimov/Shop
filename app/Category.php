<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed photo
 * @property mixed name
 */
class Category extends Model
{
    protected $fillable = [
        "default", "name", "parent", "photo"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function photoPath()
    {
        if (!$this->photo) {
            return null;
        }
        $path = $this->hasOne("App\PhotoBinding", "id", "photo")
            ->first()
            ->photos
            ->pluck("filename")
            ->first();

        $path = url('/') . "/storage/" . $path;
        return $path;
    }

    public function translations()
    {
        return $this->hasOne("App\TranslationBinding", "id", "name")
            ->first()
            ->translations
            ->pluck("value", "code");
    }

    public function parentCategory()
    {
        return $this->hasOne("App\Category", "id", "parent");
    }

    public function normalize()
    {
        $this->name = $this->translations();
        $this->photo = $this->photoPath();
    }
}
