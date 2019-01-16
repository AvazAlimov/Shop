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
use Illuminate\Support\Facades\Storage;
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

    //Function to update a product
    public function update(Request $request, $id)
    {
        //Finding a product
        $product = Product::find($id);
        if (!$product) {
            return response()->json([], 404);
        }

        //Validating a body of a request
        $validation = Validator::make($request->all(), [
            "translations" => [new TranslationsRule],
            "descriptions" => [new TranslationsRule],
            "photo.*" => "mimes:jpeg,jpg,png,gif|max:" . self::FILE_SIZE,
            "brand" => [new BrandRule],
            "season" => [new SeasonRule],
            "category" => [new CategoryRule],
            "collection" => [new CollectionRule]
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Updating a default name
        if ($request->get("default")) {
            $product->default = $request->get("default");

            //Saving changes
            $product->save();
        }

        //Updating photos
        if ($request->file("photo")) {
            //Deleting old photos
            if ($product->photo) {
                foreach ($product->photoBindings->photos as $photo) {
                    Storage::delete("public/" . $photo->filename);
                    Photo::destroy($photo->id);
                }
            }

            //Inserting new photos
            foreach ($request->file("photo") as $photo) {
                //Inserting an image into storage
                $path = $photo->storeAs("public/products", $photo->hashName());
                $path = str_replace("public/", "", $path);

                //Creating a new photo
                Photo::create([
                    "filename" => $path,
                    "binding" => $product->photo
                ]);
            }

            //Saving changes
            $product->save();
        }

        //Updating translations
        if ($request->get("translations")) {
            //Deleting old translations
            foreach ($product->translationBindings->translations as $translation) {
                Translation::destroy($translation->id);
            }

            //Inserting new translations
            foreach ($request->get("translations") as $code => $translation) {
                Translation::create([
                    "code" => $code,
                    "value" => $translation,
                    "binding" => $product->name
                ]);
            }

            //Saving changes
            $product->save();
        }

        //Updating translations
        if ($request->get("descriptions")) {
            //Deleting old translations
            foreach ($product->descriptionBindings->translations as $translation) {
                Translation::destroy($translation->id);
            }

            //Inserting new translations
            foreach ($request->get("translations") as $code => $translation) {
                Translation::create([
                    "code" => $code,
                    "value" => $translation,
                    "binding" => $product->description
                ]);
            }

            //Saving changes
            $product->save();
        }

        //Updating brand
        $product->brand = $request->get("brand");
        //Updating season
        $product->season = $request->get("season");
        //Updating collection
        $product->collection = $request->get("collection");
        //Updating category
        $product->category = $request->get("category");
        $product->save();

        //Normalizing product for response
        $product = Product::find($product->id);
        $product->normalize();

        //Returning updated product
        return response()->json($product, 200);
    }

    //Function to delete a product
    public function delete($id)
    {
        //Finding a product from a database
        $product = Product::find($id);
        if (!$product) {
            return response()->json([], 404);
        }

        //Deleting photos
        if ($product->photo) {
            foreach ($product->photoBindings->photos as $photo) {
                Storage::delete("public/" . $photo->filename);
                Photo::destroy($photo->id);
            }
            PhotoBinding::destroy($product->photo);
        }

        //Deleting translations
        TranslationBinding::destroy($product->name);
        //Deleting descriptions
        TranslationBinding::destroy($product->description);

        //Returning a success response
        return response()->json([], 200);
    }

    public function getAll()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $product->normalize();
        }

        return response()->json($products, 200);
    }

    //Function to get a season
    public function get($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([], 404);
        }
        $product->normalize();

        //Returning a season
        return response()->json($product, 200);
    }
}
