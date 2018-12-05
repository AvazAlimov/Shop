<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed photo
 * @property mixed name
 */
class Season extends Model
{
    protected $fillable = [
        "name", "default", "photo"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function photoPath()
    {
        if (!$this->photo) {
            return null;
        }
        return $this->hasOne("App\PhotoBinding", "id", "photo")
            ->first()
            ->photos
            ->pluck("filename")
            ->first();
    }

    public function translations()
    {
        return $this->hasOne("App\TranslationBinding", "id", "name")
            ->first()
            ->translations
            ->pluck("value", "code");
    }

    public function normalize()
    {
        $this->name = $this->translations();
        $this->photo = $this->photoPath();
    }
}
