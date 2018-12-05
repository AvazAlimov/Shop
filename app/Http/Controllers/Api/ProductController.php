<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\Photo;
use App\PhotoBinding;
use App\Product;
use App\Rules\BrandRule;
use App\Rules\CategoryRule;
use App\Rules\CollectionRule;
use App\Rules\SeasonRule;
use App\Rules\TranslationsRule;
use App\Translation;
use App\TranslationBinding;
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

        $photo_binding = null;
        if ($request->file("photo")) {
            $photo_binding = PhotoBinding::create();
            foreach ($request->file("photo") as $photo) {
                //Inserting an image into storage
                $path = $photo->storeAs("public/products", $photo->hashName());
                $path = str_replace("public/", "", $path);

                //Creating a new photo
                Photo::create([
                    "filename" => $path,
                    "binding" => $photo_binding->id
                ]);
            }
        }

        $description_binding = TranslationBinding::create();
        foreach ($request->get("descriptions") as $code => $value) {
            Translation::create([
                "code" => $code,
                "value" => $value,
                "binding" => $description_binding->id
            ]);
        }

        $name_binding = TranslationBinding::create();
        foreach ($request->get("translations") as $code => $value) {
            Translation::create([
                "code" => $code,
                "value" => $value,
                "binding" => $name_binding->id
            ]);
        }

        $product = Product::create([
            "default" => $request->get("default"),
            "name" => $name_binding->id,
            "description" => $description_binding->id,
            "photo" => $photo_binding == null ? null : $photo_binding->id,
            "brand" => $request->get("brand"),
            "season" => $request->get("season"),
            "category" => $request->get("category"),
            "collection" => $request->get("collection")
        ]);
        $product->normalize();

        return response()->json($product, 200);
    }
}
