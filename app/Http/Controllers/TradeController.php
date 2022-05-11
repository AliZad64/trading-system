<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeRequest;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Trade;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $profile = $request->user()->profile;

        //get all trades related to user
        $trade = Trade::with(['itemSendObject'=> function($query) use ($profile){
            $query->where('profile_id', 'like', $profile->id);
        }])->get();

        return response()->json($trade,200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, TradeRequest $payload)
    {
        $profile = $request->user()->profile;

        try {
            $itemSend = Item::where('id','=',$payload->itemSend)->where('profile_id','=',$profile->id)->firstOrFail();


            $itemReceive = Item::findOrFail($payload->itemReceive);
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }
        //check if the item you taking is not yours
        if ($itemReceive->profile->id == $profile->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
//        echo $itemReceive->profile->id;
        $trade = Trade::create([
            'itemSend' => $itemSend->id,
            'itemReceive' => $itemReceive->id,
            'confirmation' => $itemReceive->profile->id
        ]);
        return response()->json($trade,201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $profile = $request->user()->profile;
        $trade = Trade::find($id);

        if ($trade->itemSendObject->profile->id == $profile->id || $trade->itemReceiveObject->profile->id == $profile->id)
        {
            return response()->json($trade,200);
        }
        return response()->json("unauthorized",403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,TradeRequest $payload, $id)
    {

        $profile = $request->user()->profile;
        try {
            $itemSend = Item::where('id','=',$payload->itemSend)->where('profile_id','=',$profile->id)->firstOrFail();


            $itemReceive = Item::findOrFail($payload->itemReceive);
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }



        //check if the item you are taking is not yours
        if ($itemReceive->profile->id == $profile->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
        Trade::where('id','=',$id)->where('confirmation', '=', $profile->id)->firstOrFail()->update([
            'itemSend' => $itemSend->id,
            'itemReceive' => $itemReceive->id,
            'confirmation' => $itemReceive->profile->id
        ]);
        //check if confirmation by the received user
        return response()->json('trade successfully',201);
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
}
