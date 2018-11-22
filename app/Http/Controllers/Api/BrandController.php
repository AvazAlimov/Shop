<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\Brand;
use App\Photo;
use App\PhotoBinding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    //Function to create a new brand
    public function create(Request $request)
    {
        //Validating the request body
        $validation = Validator::make($request->all(), [
            "name" => "required",
            "logo" => "mimes:jpeg,jpg,png,gif|required|max:2000"
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Inserting an image into storage: brands and get path
        $path = $request->file("logo")->storeAs("public/brands", $request->file("logo")->hashName());
        $path = str_replace("public/", "", $path);

        //Creating a new photo binding
        $photo_binding = PhotoBinding::create();

        //Creating a new photo
        Photo::create([
            "filename" => $path,
            "binding" => $photo_binding->id
        ]);

        //Creating a new brand
        $brand = Brand::create([
            "name" => $request->get("name"),
            "logo" => $photo_binding->id
        ]);
        $brand->logo = $brand->logoPath();

        //Returning created object
        return response()->json($brand, 201);
    }
}
