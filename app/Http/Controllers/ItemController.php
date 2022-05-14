<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;

use http\Client\Curl\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ItemController extends Controller
{

    public function allItems()
    {
        return ItemResource::collection(Item::all());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return ItemResource::collection($user->items);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request ,ItemRequest $payload)
    {
//        $imageFile = $payload->image;
//
//        $imageName = time().'.'.$payload->image->extension();
//        $getImage = $payload->image;
//        $getImage->move(public_path('images'), $imageName);
        $item = new Item();
        $item['user_id'] = $request->user()->id;
        $item['name'] = $payload->name;
        $item->save();
        echo $item->imageColumn;
        echo $item->image;
        $item2 = Item::find($item->id);
        $item2->update(['image->key' => $payload->image]);
        echo $item2->image;
        echo $item2->imagesPath();

        //commented code doesn't work and i don't know why
//        $item = Item::create([
//            'user_id' => $request->user()->id,
//            'name' => $payload->name,
//            'image' => "testing"
//        ]);
        return new ItemResource($item2);
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
        return new ItemResource($item);
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
        $item->image = $payload->image;
        $item->save();
        return new ItemResource($item);

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
