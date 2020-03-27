<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterAuthRequest;
use App\Pasien;
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
            $user->nomr = $request->nomorMr;
            $user->name = $request->nama;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            if ($user->save()) {
                $pasien = new Pasien();
                $pasien->id_user = $user->id;
                $pasien->nama = $user->name;
                $pasien->save();

                return response()->json([
                    'success' => true,
                    'id' => $user->id,
                    'name' => $user->name,
                    'message' => 'Selamat akun anda sudah terdaftar, silahkan login'
                ], 200);

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Koneksi Jaringan'
                ], 200);
            }

//                Mail::send('mail', ['nama' => $request->email, 'pesan' => 'admin123'], function ($message) use ($request)
//                {
//                    $message->from('rsud.pp@padangpanjang.go.id', 'RSUD PADANG PANJANG');
//                    $message->to($request->email);
//                });

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
        } else {
            $getUser = User::where('email', $request->email)->first();

            if ($getUser->login == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ini telah login pada perangkat lain',
                ], 401);
            } else {
                if ($getUser->nomr == 0) {
                    $user = new User();

                    $data = [
                        'login' => 1,
                    ];

                    if ($user->where('id', $getUser->id)->update($data)) {
                        if($getUser->nomr == 0){
                            return response()->json([
                                'success' => true,
                                'status' => true,
                                'token' => $jwt_token,
                            ]);
                        }else{
                            return response()->json([
                                'success' => true,
                                'status' => false,
                                'token' => $jwt_token,
                            ]);
                        }

                    } else {
                        return response()->json([
                            'message' => 'Gangguan Jaringan',
                            'success' => false,
                        ], 401);
                    }
                } else {
                    $user = new User();

                    $data = [
                        'login' => 1,
                    ];

                    if ($user->where('id', $getUser->id)->update($data)) {
                        return response()->json([
                            'success' => true,
                            'token' => $jwt_token,
                        ]);
                    } else {
                        return response()->json([
                            'message' => 'Gangguan Jaringan',
                            'success' => false,
                        ], 401);
                    }
                }


            }

        }

    }


    public function logout(Request $request)
    {

        $getUser = User::where('id', $request->id)->first();

        $user = new User();

        $data = [
            'login' => 0,
        ];

        if ($user->where('id', $getUser->id)->update($data)) {
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
        } else {
            return response()->json([
                'message' => 'Gangguan Jaringan',
                'success' => false,
            ], 401);
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

        $getUser = compact('user');

        if ($getUser['user']['nomr'] === 0) {
            return response()->json([
                'dataUser' => compact('user'),
                'dataProfile' => ['data']
            ], 401);
        } else {
            $getPasien = Pasien::where('nomr', $getUser['user']['nomr'])->first();
            return response()->json([
                'dataUser' => compact('user'),
                'dataProfile' => $getPasien,
            ], 401);
        }


    }
}
