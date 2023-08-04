<?php

namespace App\Http\Controllers\API;

use App\Models\Kit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Kit::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $kit = new Kit;
        $kit->name = $request->name;
        $kit->quantity = $request->quantity;
        $kit->state = $request->state;
        $kit->save();
        return ['message'=>'kit created'];
    }

    /**
     * Display the specified resource.
     */
    public function show(int $idKit)
    {
        $kit = Kit::find($idKit);
        return $kit;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,int $idKit)
    {
        $kit = Kit::find($idKit);
        $kit->name = $request->name;
        $kit->quantity = $request->quantity;
        $kit->state = $request->state;
        $kit->save();
        return ['message'=>'kit updated'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $idKit)
    {
        $kit = Kit::find($idKit);
        $kit->state = 'ELIM';
        $kit->save();
        return ['message'=>'kit eliminated'];
    }
}
