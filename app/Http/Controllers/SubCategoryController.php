<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Category;
use App\SubCategory;
use App\Log;
use App\Http\Resources\SubCategory as SubCategoryResource;

class SubCategoryController extends Controller
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
        $category = Category::orderBy('name', 'asc');
        if ($request -> input('offset')) {
            if (!$request -> input('limit'))
                return response()->json([
                    'success'   => false,
                    'messages'  => 'You must include limit parameter!',
                ], 400);
            $category = $category -> offset($request -> input('offset'));
        }
        if ($request -> input('limit'))
            $category = $category -> limit($request -> input('limit'));
        $category = $category -> get();
        return response()->json([
            'success'   => true,
            'messages'  => 'List of All Sub Categories',
            'data'      => SubCategoryResource::collection($category),
            'total'     => Category::count()
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
            'category_id'   => 'required|exists:categories,id',
            'name'          => 'required|max:50|unique:sub_categories',
        ]);

        $subCategory = SubCategory::create($request->all());
        
        if ($subCategory) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_SUB_CATEGORY_CREATE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Create new sub category: ' . $subCategory -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Create new sub category: '.$subCategory -> name,
                'data'      => SubCategory::join('categories', 'sub_categories.category_id', '=', 'categories.id')->select('sub_categories.*', 'categories.name as categories_name')->find($subCategory->id)
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Create Sub Category Fail!',
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
            'messages'  => 'Detail of Sub Category',
            'data'      => SubCategory::join('categories', 'sub_categories.category_id', '=', 'categories.id')->select('sub_categories.*', 'categories.name as categories_name')->find($id)
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
            'category_id'   => 'exists:categories,id',
            'name'          => 'max:50|unique:sub_categories,name,'.$id,
        ]);

        $subCategory = SubCategory::findOrFail($id);
        $logInfo = 'Update Sub Category';
        $changed = false;

        if ($request -> category_id != $subCategory -> category_id) {
            $logInfo .= ' \n Category: ' . Category::find($subCategory -> category_id)->name . ' into ' . Category::find($request -> category_id)->name;
            $changed = true;
        }
        if ($request -> name != $subCategory -> name ) {
            $logInfo .= ' \n Name: ' . $subCategory -> name . ' into ' . $request -> name;
            $changed = true;
        }

        if (!$changed) {
            return response()->json([
                'success'   => false,
                'messages'  => 'No Sub Category Changes!',
            ], 400);
        }

        $subCategory -> update($request->all());

        $log = Log::create([
            'log_sub_type_id'   => env('LOG_SUB_CATEGORY_UPDATE'),
            'user_id'           => Auth::user() -> id,
            'information'       => $logInfo
        ]);
        return response()->json([
            'success'   => true,
            'messages'  => 'Update Sub Category Successfully!',
            'data'      => $subCategory
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
        $subCategory = SubCategory::findOrFail($id);

        if ($subCategory -> delete()) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_SUB_CATEGORY_DELETE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Delete category: '.$subCategory -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete Sub Category Successfully!',
            ], 200);
        }
    }
}