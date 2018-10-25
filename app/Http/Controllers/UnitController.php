<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Unit;
use App\Log;

class UnitController extends Controller
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
            'messages'  => 'List of All Unit',
            'data'      => Unit::all()
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
            'name'  => 'required|max:50|unique:units',
            'value' => 'required|integer|min:0'
        ]);

        $unit = Unit::create($request->all());
        
        if ($unit) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_UNIT_CREATE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Create new unit: ' . $unit -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Create new unit '.$unit -> name,
                'data'      => $unit
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'messages'  => 'Create Unit Fail!',
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
            'messages'  => 'Detail of Unit',
            'data'      => Unit::find($id)
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
            'name'  => 'required|max:50|unique:units,name,'.$id,
            'value' => 'required|integer|min:0'
        ]);

        $unit = Unit::findOrFail($id);
        $logInfo = 'Update Unit';
        $changed = false;

        if ($request -> name != $unit -> name ) {
            $logInfo .= ' \n Name: ' . $unit -> name . ' into ' . $request -> name;
            $changed = true;
        }
        if ($request -> value != $unit -> value) {
            $logInfo .= ' \n Value: ' . $unit -> value . ' into ' . $request -> value;
            $changed = true;
        }

        if (!$changed) {
            return response()->json([
                'success'   => false,
                'messages'  => 'No Unit Changes!',
            ], 400);
        }

        $unit -> update($request->all());

        $log = Log::create([
            'log_sub_type_id'   => env('LOG_UNIT_UPDATE'),
            'user_id'           => Auth::user() -> id,
            'information'       => $logInfo
        ]);
        return response()->json([
            'success'   => true,
            'messages'  => 'Update Unit Successfully!',
            'data'      => $unit
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
        $unit = Unit::findOrFail($id);

        if ($unit -> delete()) {
            $log = Log::create([
                'log_sub_type_id'   => env('LOG_UNIT_DELETE'),
                'user_id'           => Auth::user() -> id,
                'information'       => 'Delete unit: '.$unit -> name
            ]);
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete Unit Successfully!',
            ], 200);
        }
    }
}