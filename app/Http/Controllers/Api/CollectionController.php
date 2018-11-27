<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\Collection;
use App\Photo;
use App\PhotoBinding;
use App\Rules\TranslationsRule;
use App\Translation;
use App\TranslationBinding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    const FILE_SIZE = "20000";

    //Function to create a new collection
    public function create(Request $request)
    {
        //Validating a body of a request
        $validation = Validator::make($request->all(), [
            "default" => "required",
            "translations" => ["required", new TranslationsRule],
            "photo.*" => "mimes:jpeg,jpg,png,gif|max:" . self::FILE_SIZE
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Inserting photos
        $photo_binding = null;
        if ($request->file("photo")) {
            //Creating a new photo binding
            $photo_binding = PhotoBinding::create();
            foreach ($request->file("photo") as $photo) {
                //Inserting an image into storage
                $path = $photo->storeAs("public/collections", $photo->hashName());
                $path = str_replace("public/", "", $path);

                //Creating a new photo
                Photo::create([
                    "filename" => $path,
                    "binding" => $photo_binding->id
                ]);
            }
        }

        //Inserting all translations of a name
        $translation_binding = TranslationBinding::create();
        foreach ($request->get("translations") as $code => $translation) {
            Translation::create([
                "code" => $code,
                "value" => $translation,
                "binding" => $translation_binding->id
            ]);
        }

        //Creating a new collection
        $collection = Collection::create([
            "default" => $request->get("default"),
            "name" => $translation_binding->id,
            "photo" => $photo_binding == null ? null : $photo_binding->id
        ]);
        $collection->photo = $collection->photosPath();
        $collection->name = $collection->nameTranslations();

        //Returning a collection
        return response()->json($collection, 200);
    }

    public function delete($id)
    {
        //Finding a collection from a database
        $collection = Collection::find($id);
        if (!$collection) {
            return response()->json([], 404);
        }

        //Deleting photos
        if ($collection->photo) {
            foreach ($collection->photoBindings->photos as $photo) {
                Storage::delete("public/" . $photo->filename);
                Photo::destroy($photo->id);
            }
            PhotoBinding::destroy($collection->photo);
        }

        //Deleting translations and a collection
        TranslationBinding::destroy($collection->name);

        //Returning a success response
        return response()->json([], 200);
    }
}
