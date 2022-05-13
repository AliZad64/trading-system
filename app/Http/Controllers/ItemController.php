<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;

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
        $user = $request->user();
        return response()->json($user->item, 200);
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
        $item = Item::create([
            'name' => $payload->name,
            'user_id' => $user->id,
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
        try {
            $item = Item::where('id', $id)->where('user_id', $user->id)->firstOrFail();
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
        $user = $request->user();
        try {
            $item = Item::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }
        $item->delete();
        return response()->json('item deleted',201);
    }
}
