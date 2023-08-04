<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::where('state', "ACT")->get();;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->secondname = $request->secondname;
        $user->email = $request->email;
        $user->lastname = $request->lastname;
        $user->gender = $request->gender;
        $user->civilstatus = $request->civilstatus;
        $user->age = $request->age;
        $user->birthday = $request->birthday;
        $user->state = $request->state;
        $user->save();
        return ['message'=>'user created'];
    }

    /**
     * Display the specified resource.
     */
    public function show(int $iduser)
    {
        $user = User::find($iduser);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,int $idUser)
    {
        $user = User::find($idUser);
        $user->name = $request->name;
        $user->secondname = $request->secondname;
        $user->email = $request->email;
        $user->lastname = $request->lastname;
        $user->gender = $request->gender;
        $user->civilstatus = $request->civilstatus;
        $user->age = $request->age;
        $user->birthday = $request->birthday;
        $user->state = $request->state;
        $user->save();
        return ['message'=>'user updated'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $iduser)
    {
        $user = User::find($iduser);
        $user->state = 'ELIM';
        $user->save();
        return ['message'=>'user eliminated'];

    }

    public function addQuantity(Request $request){
        $user = User::find($request->id);  
    }
}
