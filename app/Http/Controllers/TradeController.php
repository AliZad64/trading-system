<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeRequest;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Trade;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $profile = Profile::where('user_id', $user->id)->first();
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
    public function store(Request $request, TradeRequest $items)
    {
        $user = $request->user();

        $profile = Profile::where('user_id',$user->id)->first();

        $itemSend = Item::find($items->itemSend);

        $itemReceive = Item::find($items->itemReceive);

        //check if item exists
        if (!$itemSend || !$itemReceive){
            return response()->json("item doesn't exist",404);
        }
        //check if the item you are sending is yours
        if ($itemSend->profile->id != $profile->id)
        {
            return response()->json('this is not your item',400);
        }
        //check if the item you taking is not yours
        if ($itemReceive->profile->id == $profile->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
//        echo $itemReceive->profile->id;
        $trade = new Trade;
        $trade['itemSend'] = $itemSend->id;
        $trade['itemReceive '] = $itemReceive->id;
        $trade['confirmation'] = $itemReceive->profile->id;
        $trade->save();
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
        $user = $request->user();
        $profile = Profile::where('user_id',$user->id)->first();
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
        $user = $request->user();
        $profile = Profile::where('user_id',$user->id)->first();
        $itemSend = Item::find($payload->itemSend);

        $itemReceive = Item::find($payload->itemReceive);

        //check if item exists
        if (!$itemSend || !$itemReceive){
            return response()->json("item doesn't exist",404);
        }
        //check if the item you are sending is yours
        if ($itemSend->profile->id != $profile->id)
        {
            return response()->json('this is not your item',400);
        }
        //check if the item you taking is not yours
        if ($itemReceive->profile->id == $profile->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
        $trade = Trade::find($id);
        //check if confirmation by the received user
        if($trade->confirmation == $profile->id)
        {
            $trade['itemSend'] = $itemSend->id;
            $trade['itemReceive '] = $itemReceive->id;
            $trade['confirmation'] = $itemReceive->profile->id;
            $trade->save();
            return response()->json($trade,201);
        }
        return response()->json("unauthorized",403);
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
