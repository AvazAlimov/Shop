<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    //Create a Language with unique code
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            "code" => "required|max:2|unique:languages,code"
        ]);
        if ($validation->fails()) {
            return response()->json($validation->errors(), 400);
        }

        Language::create($request->all());
        return response()->json([], 201);
    }

    //Delete a Language from the database
    public function delete($code)
    {
        Language::where("code", $code)->delete();
        return response()->json([], 200);
    }

    //Return all Languages in the database
    public function getAll()
    {
        $return = Language::pluck('code');
        return response()->json($return, 200);
    }
}
