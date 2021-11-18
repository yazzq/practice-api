<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $request) {
        $validate = \Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            $respon = [
                'status' => 'error',
                'massage' => 'Validator Error',
                'errors' => $validate->errors(),
                'content' => null,
            ];
            return response()->json($respon, 200);
        }else {
            $credentials = request(['email', 'password']);
            $credentials = Arr::add($credentials, 'status', 'aktif');
            if (!Auth::attempt($credentials)) {
                $respon = [
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'errors' => null,
                    'content' =>null,
                ];
                return response()->json($respon, 401);
            }
            $user = User::where('email', $request->email)->first();
            if (! \Hash::check($request->password, $user->password, [])){
                throw new \Exception('Error in Login');
            }


            $tokenResult = $user->createToken('token-auth')->plainTextToken;
            $respon = [
                'status' =>'success',
                'message' => 'successfully',
                'errors' => null,
                'content' => [
                    'status_code' => 200,
                    'access_token' => $tokenResult,
                    'token_type' => 'Bearer',
                ]
                ];
                return response()->json($respon, 200);
        }
    }

    public function logout(Request $request) {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        $respon = [
            'status' => 'Success',
            'message' => 'Logout Successfully',
            'errors' => null,
            'content' => null,
        ];
        return response()->json($respon, 200);
    }

    public function logoutall(Request $request) {
        $user = $request->user();
        $user->tokens()->delete();
        $respon = [
            'status' => "Success",
            'message' => "Logout all successfully",
            'errors' => null,
            'content' => null, 
        ];
        return response()->json($respon, 200);
    }

    public function register(Request $request) {
        // $validate = \Validator::make($request->all(), [
        //     'email' => ['required', 'string', 'max:255', 'unique:users'],
        //     'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required'],
        ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'OK',
            ]);
        
            event(new Registered($user));

    }

}
