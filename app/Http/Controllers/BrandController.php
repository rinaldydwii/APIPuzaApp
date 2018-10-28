<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Brand;
use App\Log;

class BrandController extends Controller
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
        $brands = Brand::orderBy('name', 'asc');
        if ($request -> input('offset')) {
            if (!$request -> input('limit'))
                return response()->json([
                    'success'   => false,
                    'messages'  => 'You must include limit parameter!',
                ], 400);
            $brands = $brands -> offset($request -> input('offset'));
        }
        if ($request -> input('limit'))
            $brands = $brands -> limit($request -> input('limit'));
        $brands = $brands -> get();
        return response()->json([
            'success'   => true,
            'messages'  => 'List of All Brands',
            'data'      => $brands,
            'total'     => Brand::count()
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
            'name' => 'required|max:50|unique:brands',
        ]);

        $brand = Brand::create($request->all());
        
        if ($brand) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_BRAND_CREATE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Create new brand: ' . $brand -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Create new brand: '.$brand -> name,
                'data'      => $brand
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Create Brand Fail!',
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
            'messages'  => 'Detail of Brand',
            'data'      => Brand::find($id)
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
            'name' => 'required|max:50|unique:brands,name,'.$id,
        ]);

        $brand = Brand::findOrFail($id);
        $logInfo = 'Update Brand';
        $changed = false;
        
        if ($request -> name != $brand -> name ) {
            $logInfo .= ' \n Name: ' . $brand -> name . ' into ' . $request -> name;
            $changed = true;
        }

        if (!$changed) {
            return response()->json([
                'success'   => false,
                'messages'  => 'No Brand Changes!',
            ], 400);
        }

        $brand -> update($request->all());

        $log = Log::create([
            'log_sub_type_id'   => env('LOG_BRAND_UPDATE'),
            'user_id'           => Auth::user() -> id,
            'information'       => $logInfo
        ]);
        return response()->json([
            'success'   => true,
            'messages'  => 'Update Brand Successfully!',
            'data'      => $brand
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
        $brand = Brand::findOrFail($id);

        if ($brand -> delete()) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_BRAND_DELETE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Delete brand: '.$brand -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete Brand Successfully!',
            ], 200);
        }
    }
}