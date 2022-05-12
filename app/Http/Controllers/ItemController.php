<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use App\Models\Profile;
use http\Client\Curl\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $profile = $request->user()->profile;
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
        $profile = $request->user()->profile;
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
        $profile = $request->user()->profile;
        try {
            $item = Item::where('id', $id)->where('profile_id', $profile->id)->firstOrFail();
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }
        if ($item->sendTrade->count() > 0 || $item->receiveTrade->count()> 0){
            return response()->json("the item is being used for trade requests right now",400);
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
        $profile = $request->user()->profile;
        try {
            $item = Item::where('id', $id)->where('profile_id', $profile->id)->firstOrFail();
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }
        $item->delete();
        return response()->json('item deleted',201);
    }
}
