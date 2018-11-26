<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\Brand;
use App\Photo;
use App\PhotoBinding;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    const FILE_SIZE = "20000";

    //Function to create a new brand
    public function create(Request $request)
    {
        //Validating the request body
        $validation = Validator::make($request->all(), [
            "name" => "required",
            "logo" => "mimes:jpeg,jpg,png,gif|required|max:" . self::FILE_SIZE
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Inserting an image into storage: brands and get path
        $logo = $request->file("logo");
        $path = $logo->storeAs("public/brands", $logo->hashName());
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

    //Function to delete a brand
    public function delete($id)
    {
        //Finding a brand from database
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([], 404);
        }

        //Deleting a logo from storage
        Storage::delete("public/" . $brand->logoPath());

        //Deleting a logo binding and brand
        PhotoBinding::destroy($brand->logo);

        //Returning success response
        return response()->json([], 200);
    }

    //Function to update a brand
    public function update(Request $request, $id)
    {
        //Finding a brand from database
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([], 404);
        }

        //Validating the request body
        $validation = Validator::make($request->all(), [
            "logo" => "mimes:jpeg,jpg,png,gif|max:" . self::FILE_SIZE
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        //Updating a name of a brand
        if ($request->get("name")) {
            $brand->name = $request->get("name");
            $brand->save();
        }

        //Updating a logo of a brand
        if ($request->file("logo")) {
            $old_photo_binding = $brand->logo;

            //Deleting an old logo from storage
            Storage::delete("public/" . $brand->logoPath());

            //Inserting a logo into storage: brands and get path
            $logo = $request->file("logo");
            $path = $logo->storeAs("public/brands", $logo->hashName());
            $path = str_replace("public/", "", $path);

            //Creating a new photo binding
            $photo_binding = PhotoBinding::create();

            //Creating a new photo
            Photo::create([
                "filename" => $path,
                "binding" => $photo_binding->id
            ]);

            //Saving changes
            $brand->logo = $photo_binding->id;
            $brand->save();

            //Deleting an old logo binding
            PhotoBinding::destroy($old_photo_binding);
        }

        //Returning an updated brand
        return response()->json($brand, 200);
    }

    //Function to get all brands
    public function getAll()
    {
        $brands = Brand::all();
        foreach ($brands as $brand) {
            $brand->logo = $brand->logoPath();
        }

        //Returning all brands
        return response()->json($brands, 200);
    }

    //Function to get a brand
    public function get($id)
    {
        $brand = Brand::find($id);
        if(!$brand) {
            return response()->json([], 404);
        }
        $brand->logo = $brand->logoPath();

        return response()->json($brand, 200);
    }
}
