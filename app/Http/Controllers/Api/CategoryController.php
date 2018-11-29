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
    const FILE_SIZE = "2000";

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
        $category->name = $category->nameTranslations();
        $category->photo = $category->photoPath();

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
}
