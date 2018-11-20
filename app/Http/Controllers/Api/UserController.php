<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => "required",
            "password" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], 401);
        }

        $input = $request->all();
        $input["password"] = bcrypt($input["password"]);
        $user = User::create($input);
        $token = $user->createToken("Bearer")->accessToken;

        return response()->json([
            "token" => $token
        ], 200);
    }

    public function login()
    {
        if (Auth::attempt(['username' => request('username'), 'password' => request('password')])) {
            $user = Auth::user();
            $token = $user->createToken('Bearer')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }
}
