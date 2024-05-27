<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|string',
                    'password' => 'required'
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

//            if(!Auth::attempt($request->only(['email', 'password']))){
//                return response()->json([
//                    'status' => false,
//                    'message' => 'Email & Password does not match with our record.',
//                ], 401);
//            }

            $user = User::where(function ($query) use ($request) {
                $query->where('email', $request->email)
                    ->orWhere('name', $request->email);
            })
                ->whereNotNull('email_verified_at')
                ->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }
            return response()->json(
                new UserResource($user)
                , 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            "message" => "logged out"
        ]);
    }

    public function getUser()
    {
        $user = Auth::user();
        dd($user);
    }


}
