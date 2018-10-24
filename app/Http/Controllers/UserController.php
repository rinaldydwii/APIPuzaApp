<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Position;
use App\Log;

/**
 * TODO:
 * - CODE FOR CHANGE AVATAR ON CHANGE PROFILE
 */

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'success'   => true,
            'messages'  => 'List of All Users',
            'data'      => User::join('positions', 'users.position_id', '=', 'positions.id')->select('users.*', 'positions.name as position_name')->get()
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'success'   => true,
            'messages'  => 'Detail of User',
            'data'      => User::find($id)
        ], 200);
    }

    /**
     * Display the self resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function showSelf()
    {
        return response()->json([
            'success'   => true,
            'messages'  => 'Detail of User Self',
            'data'      => User::find(Auth::user() -> id)->join('positions', 'users.position_id', '=', 'positions.id') -> select('users.*', 'positions.name as position_name') -> first()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $this->validate($request, [
            'email'         => 'string|email|max:255|unique:users,email,'.Auth::user() -> id,
            'username'      => 'string|max:30|unique:users,username,'.Auth::user() -> id,
            'name'          => 'string|max:255',
            'avatar'        => 'string|max:255',
            'phone_number'  => 'string|max:15',
        ]);
        
        $user = Auth::user();
        $logInfo = 'Change Profile';
        $changed = false;

        if ($request -> email && $request -> email != $user -> email) {
            $logInfo .= ' \n Email: ' . $user -> email . ' into ' . $request -> email;
            $changed = true;
        }
        if ($request -> username && $request -> username != $user -> username) {
            $logInfo .= ' \n Username: ' . $user -> username . ' into ' . $request -> username;
            $changed = true;
        }
        if ($request -> name && $request -> name != $user -> name) {
            $logInfo .= ' \n Name: ' . $user -> name . ' into ' . $request -> name;
            $changed = true;
        }
        if ($request -> phone_number && $request -> phone_number != $user -> phone_number) {
            $logInfo .= ' \n Phone: ' . $user -> phone_number . ' into ' . $request -> phone_number;
            $changed = true;
        }
        // FIXME: INTEGRASI DENGAN FRONT END REACT NATIVE
        if ($request -> avatar) {
            $logInfo .= ' \n Avatar: ' . $user -> avatar . ' into ' . $request -> avatar;
            $changed = true;
        }

        if (!$changed) {
            return response()->json([
                'success'   => false,
                'messages'  => 'No Profile Changes!',
            ], 400);
        }
        
        $user -> update($request->all());
        $log = Log::create([
            'log_sub_type_id'   => env('LOG_USER_CHANGE_PROFILE'),
            'user_id'           => Auth::user() -> id,
            'information'       => $logInfo
        ]);
        return response()->json([
            'success'   => true,
            'messages'  => 'Change Profile Successfully!',
            'data'      => $user -> join('positions', 'users.position_id', '=', 'positions.id') -> select('users.*', 'positions.name as position_name')
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password'  => 'required|string|min:6',
            'password'      => 'required|string|min:6|confirmed',
        ]);
        
        $user = User::find(Auth::user()->id);
        
        if (!Hash::check($request -> input('old_password'), $user -> password)) {
            return response()->json([
                'success'   => false,
                'messages'  => 'Old Password don\'t Match!',
            ], 400);
        }
        if ($request -> old_password == $request -> password) {
            return response()->json([
                'success'   => false,
                'messages'  => 'New Password must different than Old Password!',
            ], 400);
        }
        $user -> update([
            'password' => Hash::make($request -> password)
        ]);
        $log = Log::create([
            'log_sub_type_id'   => env('LOG_USER_CHANGE_PASSWORD'),
            'user_id'           => Auth::user() -> id,
            'information'       => 'Change password'
        ]);
        return response()->json([
            'success'   => true,
            'messages'  => 'Change Password Successfully!',
            'data'      => $user
        ], 200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePosition(Request $request, $id)
    {
        $this->validate($request, [
            'position_id'   => 'required|exists:positions,id',
        ]);

        if (Auth::user() -> position_id == env('USER_EMPLOYEE_POS')) {
            return response()->json([
                'success'   => false,
                'messages'  => 'You don\'t has access to this function.',
            ], 401);
        }
        
        $user = User::findOrFail($id);
        $userOld = clone $user;

        if ($user -> position_id == $request -> position_id) {
            return response()->json([
                'success'   => false,
                'messages'  => 'No Position Employee Changes!',
            ], 400);
        }

        $user -> update($request->all());
        $log = Log::create([
            'log_sub_type_id'   => env('LOG_USER_CHANGE_POSITION'),
            'user_id'           => Auth::user() -> id,
            'information'       => 'Change position from '.Position::find($userOld -> position_id)->name.' into '.Position::find($request -> position_id)->name
        ]);
        return response()->json([
            'success'   => true,
            'messages'  => 'Change Position Employee Successfully!',
            'data'      => $user
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user() -> position_id == env('USER_EMPLOYEE_POS')) {
            return response()->json([
                'success'   => false,
                'messages'  => 'You don\'t has access to this function.',
            ], 401);
        }
        $user = User::findOrFail($id);

        if ($user -> delete()) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_USER_DELETE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Delete user: '.$user -> username
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete Successfully!',
            ], 200);
        }
    }
}