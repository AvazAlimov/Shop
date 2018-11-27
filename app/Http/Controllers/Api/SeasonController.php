<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\Photo;
use App\PhotoBinding;
use App\Rules\TranslationsRule;
use App\Season;
use App\Translation;
use App\TranslationBinding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SeasonController extends Controller
{
    const FILE_SIZE = "20000";

    //Function to create a season
    public function create(Request $request)
    {
        //Validating the request body
        $validation = Validator::make($request->all(), [
            "default" => "required",
            "translations" => ["required", new TranslationsRule],
            "photo" => "mimes:jpeg,jpg,png,gif|max:" . self::FILE_SIZE
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Uploading a photo if it is given
        $photo_binding = null;
        if ($request->file("photo")) {
            //Inserting an image into storage: brands and get path
            $logo = $request->file("photo");
            $path = $logo->storeAs("public/seasons", $logo->hashName());
            $path = str_replace("public/", "", $path);

            //Creating a new photo binding
            $photo_binding = PhotoBinding::create();

            //Creating a new photo
            Photo::create([
                "filename" => $path,
                "binding" => $photo_binding->id
            ]);
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

        //Creating a new season
        $season = Season::create([
            "default" => $request->get("default"),
            "name" => $translation_binding->id,
            "photo" => $photo_binding != null ? $photo_binding->id : null
        ]);
        $season->name = $season->nameTranslations();
        if ($photo_binding) {
            $season->photo = $season->photoPath();
        }

        //Returning a new created season
        return response()->json($season, 200);
    }

    //Function to delete a season
    public function delete($id)
    {
        //Finding a brand from database
        $season = Season::find($id);
        if (!$season) {
            return response()->json([], 404);
        }

        //Deleting a logo from storage
        if ($season->photo) {
            Storage::delete("public/" . $season->photoPath());

            //Deleting a photo binding
            PhotoBinding::destroy($season->photo);
        }

        //Deleting translations and a season
        TranslationBinding::destroy($season->name);

        //Returning success response
        return response()->json([], 200);
    }

    //Function to update a season
    public function update(Request $request, $id)
    {
        //Finding a brand from database
        $season = Season::find($id);
        if (!$season) {
            return response()->json([], 404);
        }

        //Validating a request body
        $validation = Validator::make($request->all(), [
            "translations" => [new TranslationsRule],
            "photo" => "mimes:jpeg,jpg,png,gif|max:" . self::FILE_SIZE
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Updating a default name of a season
        if ($request->get("default")) {
            $season->default = $request->get("default");
        }

        //Updating name translations of a season
        $translations = $request->get("translations");
        if ($translations) {
            //Deleting old translations
            Translation::destroy($season->translationBindings->translations->pluck("id"));

            //Inserting all translations of a name
            foreach ($request->get("translations") as $code => $translation) {
                Translation::create([
                    "code" => $code,
                    "value" => $translation,
                    "binding" => $season->name
                ]);
            }
        }

        //Updating a photo of a season
        if ($request->file("photo") && $season->photo) {
            //Deleting old photo
            Storage::delete("public/" . $season->photoPath());
            Photo::destroy($season->photoBindings->photos->pluck("id"));

            //Inserting an image into storage: brands and get path
            $logo = $request->file("photo");
            $path = $logo->storeAs("public/seasons", $logo->hashName());
            $path = str_replace("public/", "", $path);

            //Creating a new photo
            Photo::create([
                "filename" => $path,
                "binding" => $season->photo
            ]);
        }

        //Saving changes
        $season->save();

        //Normalizing the response
        $season->photo = $season->photoPath();
        $season->name = $season->nameTranslations();

        //Returning an updated season
        return response()->json($season, 200);
    }
}
