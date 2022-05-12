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
     * MUSTAFA ALI HELP
     */
    public function index(Request $request)
    {
        $profile = $request->user()->profile;

        //get all trades related to user
        $trade = Trade::with(['itemSendObject'=> function($query) use ($profile){
            $query->where('profile_id', '=', $profile->id);
        }])->get();
//        $trade = Trade::find(2)->itemSendObject->where('profile_id',$profile->id)->get();

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
        $trade = new Trade;
        $trade['itemSend'] = $itemSend->id;
        $trade['itemReceive'] = $itemReceive->id;
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

    public function confirm(Request $request,$id)
    {
        $profile = $request->user()->profile;
        try {
            $trade = Trade::where('id',$id)->where('confirmation',$profile->id)->firstOrFail();
        }
        catch (ModelNotFoundException){
            return response()->json("trade request not found", 404);
        }
        //we create new items
        Item::create([
            'name' => $trade->itemSendObject->name,
            'profile_id'=> $trade->itemReceiveObject->profile->id,
        ]);
        Item::create([
            'name' => $trade->itemReceiveObject->name,
            'profile_id'=> $trade->itemSendObject->profile->id,
        ]);
        //we delete items so any old connection of trade request to these items will be cascaded
        Item::where('id', $trade->itemSendObject->id)->firstOrFail()->delete();
        Item::where('id', $trade->itemReceiveObject->id)->firstOrFail()->delete();
        $trade->delete();
        return response()->json('request has been confirmed successfully',201);
    }
}
