<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Position;

class PositionController extends Controller
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
            'messages'  => 'List of All Position',
            'data'      => Position::all()
        ], 200);
    }
}