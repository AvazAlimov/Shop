<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed photo
 * @property mixed collection
 * @property mixed brand
 * @property mixed season
 * @property mixed category
 * @property mixed name
 * @property mixed description
 */
class Product extends Model
{
    protected $fillable = [
        "default", "name", "description", "photo", "brand", "season", "category", "collection"
    ];

    protected $hidden = [
        "created_at", "updated_at"
    ];

    public function photoPaths()
    {
        if (!$this->photo) {
            return null;
        }
        return $this->hasOne("App\PhotoBinding", "id", "photo")
            ->first()
            ->photos
            ->pluck("filename");
    }

    public function translations($column)
    {
        return $this->hasOne("App\TranslationBinding", "id", $column)
            ->first()
            ->translations
            ->pluck("value", "code");
    }

    public function brandInformation()
    {
        $brand = $this->hasOne("App\Brand", "id", "brand")->first();
        $brand->normalize();
        return $brand;
    }

    public function seasonInformation()
    {
        $season = $this->hasOne("App\Season", "id", "season")->first();
        $season->normalize();
        return $season;
    }

    public function categoryInformation()
    {
        $category = $this->hasOne("App\Category", "id", "category")->first();
        $category->normalize();
        return $category;
    }

    public function collectionInformation()
    {
        if (!$this->collection) {
            return null;
        }
        $collection = $this->hasOne("App\Collection", "id", "collection")->first();
        $collection->normalize();
        return $collection;
    }

    public function normalize()
    {
        $this->brand = $this->brandInformation();
        $this->season = $this->seasonInformation();
        $this->category = $this->categoryInformation();
        $this->collection = $this->collectionInformation();
        $this->photo = $this->photoPaths();
        $this->name = $this->translations("name");
        $this->description = $this->translations("description");
    }
}
