<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\User;
use App\Log;

class LogController extends Controller
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
    public function index(Request $request)
    {
        $logs = Log::join('log_sub_types', 'logs.log_sub_type_id', '=', 'log_sub_types.id')
                    ->join('log_types', 'log_sub_types.log_type_id', '=', 'log_types.id')
                    ->join('users', 'logs.user_id', '=', 'users.id')
                    ->select('logs.id', 'log_sub_types.name as log_sub_type_name', 'log_types.name as log_type_name', 'logs.information',  'users.name as user_name', 'logs.created_at', 'logs.updated_at')
                    ->orderBy('created_at','desc');
        return response()->json([
            'success'   => true,
            'messages'  => 'List of All Logs!',
            'data'      => $request -> input('limit')
            // $log -> get()
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $this->validate($request, [
    //         'name' => 'required|max:15|unique:categories',
    //     ]);
    //     $category = Category::create($request->all());

    //     return response()->json($category, 201);
    // }
    // /**
    //  * Display the specified resource.
    //  *
    //  * @param int $id
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     return response()->json(Category::find($id));
    // }
    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param \Illuminate\Http\Request $request
    //  * @param int                      $id
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     $category = Category::findOrFail($id);
    //     $category -> update($request->all());

    //     return response()->json($category, 200);
    // }
    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param int $id
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     Category::findOrFail($id) -> delete();
    //     return response()->json([
    //         'success'   => true,
    //         'messages'  => 'Delete Successfully!',
    //     ], 200);
    // }
}