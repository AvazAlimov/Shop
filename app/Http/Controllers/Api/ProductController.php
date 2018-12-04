<?php

namespace App\Http\Controllers\Api;

use App\Rules\BrandRule;
use App\Rules\CategoryRule;
use App\Rules\CollectionRule;
use App\Rules\SeasonRule;
use App\Rules\TranslationsRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    const FILE_SIZE = "20000";

    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "default" => "required",
            "translations" => ["required", new TranslationsRule],
            "descriptions" => ["required", new TranslationsRule],
            "photo.*" => "mimes:jpeg,jpg,png,gif|max:" . self::FILE_SIZE,
            "brand" => ["required", new BrandRule],
            "season" => ["required", new SeasonRule],
            "category" => ["required", new CategoryRule],
            "collection" => [new CollectionRule]
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        return response()->json([], 200);
    }
}
