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
}
