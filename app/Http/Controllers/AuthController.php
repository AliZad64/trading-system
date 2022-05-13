<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\AuthControllerRequest;
use App\Http\Requests\AuthLoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfileUpdateRequest $updateValue)
    {

        $user = $request->user();
        $user = Profile::where('user_id',$user->id)->first();
        $user->birthdate = $updateValue->birthdate;
        $user->age = $updateValue->age;
        $user->save();
        return response()->json([
            'status'=> 'ok',
            'data'=>[
                'profile'=> $user,
            ]

        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function createAccount(AuthControllerRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'password' => bcrypt($request['password']),
            'email' => $request['email']
        ]);

        $token = $user->createToken('tokens')->plainTextToken;
        return response()->json([
            'data'=> new UserResource($user),
            'token' => $token,
        ], 201);
    }
    public function login( AuthLoginRequest $request)
    {
        $attr = $request->validated();
        if (!Auth::attempt($attr)) {
            return response()->json([
                'error'=> 'user not found',
            ], 400);
//            return $this->error('Credentials not match', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;
        return response()->json([
            'data'=> new UserResource($user),
            'token' => $token,
        ], 201);

    }
    public function Logout(Request $request)
    {
        request()->user()->tokens()->delete();
        return response()->json([
            'success'=> 'user has logged out successfully',
        ], 201);
    }
}
