<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed logoBindings
 */
class Brand extends Model
{
    protected $fillable = [
        "name", "logo"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function logoPath()
    {
        $path = $this->hasOne("App\PhotoBinding", "id", "logo")
            ->first()
            ->photos
            ->pluck("filename")
            ->first();

        $path = url('/') . "/storage/" . $path;
        return $path;
    }

    public function normalize()
    {
        $this->logo = $this->logoPath();
    }
}
