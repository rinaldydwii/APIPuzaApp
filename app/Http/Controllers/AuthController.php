<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Log;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['only' => 'register']);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'email'         => 'required|string|email|max:255|unique:users',
            'username'      => 'required|string|max:30|unique:users',
            'name'          => 'required|string|max:255',
            'position_id'   => 'required|exists:positions,id',
            'password'      => 'required|string|min:6|confirmed',
        ]);

        if (Auth::user() -> position_id == env('USER_EMPLOYEE_POS')) {
            return response()->json([
                'success'   => false,
                'messages'  => 'You don\'t has access to this function.',
            ], 401);
        }

        $user = User::create([
            'email'         => $request -> input('email'),
            'username'      => $request -> input('username'),
            'name'          => $request -> input('name'),
            'position_id'   => $request -> input('position_id'),
            'password'      => Hash::make($request -> input('password'))
        ]);
        
        if ($user) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_USER_CREATE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Create new user: ' . $user -> username
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Register Success!',
                'data'      => $user,
            ], 201);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Register Fail!',
            ], 400);
        }
    }

    /**
     * Index login controller
     *
     * When user success login will retrive callback as api_token
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'username'      => 'required|string|max:30',
            'password'      => 'required|string|min:6',
        ]);

        $username   = $request -> input('username');
        $password   = $request -> input('password');

        $user = User::where('username', $username) -> first();

        if (!$user) {
            return response()->json([
                'success'   => false,
                'messages'  => 'Account not found!',
            ], 400);
        }

        if (Hash::check($password, $user -> password)) {
            $apiToken = base64_encode(str_random(40));
            
            $user -> update([
                'api_token' => $apiToken
            ]);
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_USER_LOGIN'),
                'user_id'           => $user -> id,
                'information'       => 'Login user'
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Login Success!',
                'data'      => [
                    'user' => $user,
                    'api_token' => $apiToken
                ],
            ], 201);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Login Fail! Username and Password don\'t Match!',
            ], 400);
        }
    }
}