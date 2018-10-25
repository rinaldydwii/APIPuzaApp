<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Category;
use App\Log;

class CategoryController extends Controller
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
            'messages'  => 'List of All Categories',
            'data'      => Category::all()
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|unique:categories',
        ]);

        $category = Category::create($request->all());
        
        if ($category) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_CATEGORY_CREATE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Create new category: ' . $category -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Create new category: '.$category -> name,
                'data'      => $category
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Create Category Fail!',
            ], 400);
        }
        
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
            'messages'  => 'Detail of Category',
            'data'      => Category::find($id)
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
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:50|unique:categories,name,'.$id,
        ]);

        $category = Category::findOrFail($id);
        $logInfo = 'Update Category';
        $changed = false;
        
        if ($request -> name != $category -> name ) {
            $logInfo .= ' \n Name: ' . $category -> name . ' into ' . $request -> name;
            $changed = true;
        }

        if (!$changed) {
            return response()->json([
                'success'   => false,
                'messages'  => 'No Category Changes!',
            ], 400);
        }

        $category -> update($request->all());

        $log = Log::create([
            'log_sub_type_id'   => env('LOG_CATEGORY_UPDATE'),
            'user_id'           => Auth::user() -> id,
            'information'       => $logInfo
        ]);
        return response()->json([
            'success'   => true,
            'messages'  => 'Update Category Successfully!',
            'data'      => $category
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
        $category = Category::findOrFail($id);

        if ($category -> delete()) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_CATEGORY_DELETE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Delete category: '.$category -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete Category Successfully!',
            ], 200);
        }
    }
}