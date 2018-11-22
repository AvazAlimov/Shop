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

    public function products()
    {
        return $this->hasMany("App\Product", "brand", "id");
    }

    protected function logoBindings()
    {
        return $this->hasOne("App\PhotoBinding", "id", "logo");
    }

    public function logoPath()
    {
        $path = $this->logoBindings->photos->pluck("filename")->first();
        unset($this->logoBindings);
        return $path;
    }
}
