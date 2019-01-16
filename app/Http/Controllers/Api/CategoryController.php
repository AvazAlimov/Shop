<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\Category;
use App\Photo;
use App\PhotoBinding;
use App\Rules\CategoryRule;
use App\Rules\TranslationsRule;
use App\Translation;
use App\TranslationBinding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    const FILE_SIZE = "20000";

    //Function to create a category
    public function create(Request $request)
    {
        //Validating a request body
        $validation = Validator::make($request->all(), [
            "default" => "required",
            "photo" => "mimes:jpeg,jpg,png,gif|required|max:" . self::FILE_SIZE,
            "translations" => ["required", new TranslationsRule],
            "parent" => [new CategoryRule]
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Inserting name translations
        $translation_binding = TranslationBinding::create();
        foreach ($request->get("translations") as $code => $value) {
            Translation::create([
                "code" => $code,
                "value" => $value,
                "binding" => $translation_binding->id
            ]);
        }

        //Uploading a photo if it is given
        $photo_binding = null;
        if ($request->file("photo")) {
            //Inserting an image into storage
            $logo = $request->file("photo");
            $path = $logo->storeAs("public/categories", $logo->hashName());
            $path = str_replace("public/", "", $path);

            //Creating a new photo binding
            $photo_binding = PhotoBinding::create();

            //Creating a new photo
            Photo::create([
                "filename" => $path,
                "binding" => $photo_binding->id
            ]);
        }

        //Creating a new category
        $category = Category::create([
            "default" => $request->get("default"),
            "name" => $translation_binding->id,
            "photo" => $photo_binding == null ? null : $photo_binding->id,
            "parent" => $request->get("parent") == null ? null : $request->get("parent")
        ]);
        $category->normalize();

        //Returning a new created category
        return response()->json($category, 200);
    }

    //Function to delete a category
    public function delete($id)
    {
        //Finding a category
        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 404);
        }

        //Deleting a photo
        if ($category->photo) {
            Storage::delete("public/" . $category->photoPath());
            PhotoBinding::destroy($category->photo);
        }

        //Deleting translations and a category
        TranslationBinding::destroy($category->name);

        //Returning a success response
        return response()->json([], 200);
    }

    //Function to updated a category
    public function update(Request $request, $id)
    {
        //Finding a category from a database
        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 404);
        }

        //Validating a request body
        $validation = Validator::make($request->all(), [
            "photo" => "mimes:jpeg,jpg,png,gif|max:" . self::FILE_SIZE,
            "translations" => [new TranslationsRule],
            "parent" => [new CategoryRule]
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Updated a default property
        if ($request->get("default")) {
            $category->default = $request->get("default");

            //Saving changes
            $category->save();
        }

        //Update a photo
        if ($request->file("photo")) {
            //Deleting old photo
            if ($category->photo) {
                Storage::delete("public/" . $category->photoPath());
                Photo::destroy($category->photoBindings->photos->pluck("id"));
            } else {
                $category = PhotoBinding::create()->id;
            }

            //Inserting a new photo
            $photo = $request->file("photo");
            $path = $photo->storeAs("public/categories", $photo->hashName());
            $path = str_replace("public/", "", $path);

            //Creating a new photo
            Photo::create([
                "filename" => $path,
                "binding" => $category->photo
            ]);

            //Saving changes
            $category->save();
        }

        //Updating translations
        if ($request->get("translations")) {
            //Deleting old translations
            Translation::destroy($category->translationBindings->translations->pluck("id"));

            //Inserting new translations
            foreach ($request->get("translations") as $code => $value) {
                Translation::create([
                    "code" => $code,
                    "value" => $value,
                    "binding" => $category->name
                ]);
            }

            //Saving changes
            $category->save();
        }

        //Updating a parent property
        $category->parent = $request->get("parent");
        //Saving changes
        $category->save();

        $category = Category::find($category->id);
        $category->normalize();

        //Returning an updated category
        return response()->json($category, 200);
    }

    //Function to get all categories
    public function getAll()
    {
        $categories = Category::all();
        foreach ($categories as $category) {
            $category->normalize();
        }

        //Returning all categories
        return response()->json($categories, 200);
    }

    //Function to get a category
    public function get($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([], 404);
        }
        $category->normalize();

        //Returning a category
        return response()->json($category, 200);
    }
}
