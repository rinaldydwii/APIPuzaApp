<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;

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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request, $id)
    {
        $this->validate($request, [
            'email'         => 'string|email|max:255|unique:users,email,'.$id,
            'username'      => 'string|max:30|unique:users',
            'name'          => 'string|max:255',
            'avatar'        => 'string|max:255',
            'phone_number'  => 'string|max:15',
        ]);
        $user = Auth::user();
        $user -> update($request->all());

        return response()->json([
            'success'   => true,
            'messages'  => 'Change Profile Successfully!',
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
    public function updatePassword(Request $request, $id)
    {
        $this->validate($request, [
            'old_password'  => 'required',
            'password'      => 'required|string|min:6|confirmed',
        ]);
        
        $user = Auth::user();
        if (Hash::make($request -> input('old_password')) == $user -> password)
        $user -> update($request->all());

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

        if (Auth::user() -> position_id == '1') {
            $user = User::findOrFail($id);
            $user -> update($request->all());
            return response()->json([
                'success'   => true,
                'messages'  => 'Change Position Employee Successfully!',
                'data'      => $user
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Update Fail! You don\'t has access to this function.',
            ], 401);
        }
        
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
        if (Auth::user() -> position_id != '4') {
            User::findOrFail($id) -> delete();
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete Successfully!',
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Update Fail! You don\'t has access to this function.',
            ], 401);
        }
    }
}