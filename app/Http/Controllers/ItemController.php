<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\Profile;
use http\Client\Curl\User;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    public function allItems()
    {
        return response()->json(Item::all(),200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $profile = Profile::where('user_id',$user->id)->first();
        return response()->json($profile->item, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request ,ItemRequest $payload)
    {
        $user = $request->user();
        $profile = Profile::where('user_id',$user->id)->first();
        $item = Item::create([
            'name' => $payload->name,
            'profile_id' => $profile->id,
        ]);
        return response()->json($item,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::find($id);
        return response()->json($item,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, ItemRequest $payload)
    {
        $user = $request->user();
        $profile = Profile::where('user_id',$user->id)->first();
        $item = Item::find($id);
        if (!$item) {
            return response()->json("item doesn't exist",404);
        }
        if ($item->profile->id != $profile->id)
        {
            return response()->json('unauthorized',403);
        }
        $item->name = $payload->name;
        $item->save();
        return response()->json($item,200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $user = $request->user();
        $profile = Profile::where('user_id',$user->id)->first();
        $item = Item::find($id);
        if (!$item) {
            return response()->json("item doesn't exist",404);
        }
        if ($item->profile != $profile->id)
        {
            return response()->json('unauthorized',403);
        }
        $item->delete();
        return response()->json('item deleted',204);
    }
}
