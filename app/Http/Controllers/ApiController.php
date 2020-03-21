<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\User;
use Illuminate\Support\Facades\Hash;
use Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{
    public $loginAfterSignUp = true;

    public function register(Request $request)
    {

        if (User::where('email', $request->email)->count() == 0) {



                $user = new User();
                $user->name = $request->nama;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();



//                Mail::send('email.mails', ['nama' => $request->email, 'pesan' => 'admin123'], function ($message) use ($request)
//                {
//                    $message->from('odhiahmad15@gmail.com', 'Odhi');
//                    $message->to($request->email);
//                });



                return response()->json([
                    'success' => true,
                    'id' => $user->id,
                    'name' => $user->name,
                    'message' => 'Selamat akun anda sudah terdaftar'
                ], 200);


        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email Sudah terdaftar'
            ], 200);
        }

    }



    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
        $jwt_token = null;

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], 401);
        }




        return response()->json([
            'success' => true,
            'token' => $jwt_token,
        ]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

    public function getAuthUser(Request $request)
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
}
