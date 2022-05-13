<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeRequest;
use App\Models\Item;

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
        $user = $request->user();

        //get all trades related to user
        $trade = Trade::with(['itemSendObject'=> function($query) use ($user){
            $query->where('user_id', '=', $user->id);
        }])->get();
//        $trade = Trade::find(2)->itemSendObject->where('user_id',$user->id)->get();

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
        $user = $request->user();

        try {
            $itemSend = Item::where('id','=',$payload->itemSend)->where('user_id','=',$user->id)->firstOrFail();


            $itemReceive = Item::findOrFail($payload->itemReceive);
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }
        //check if the item you taking is not yours
        if ($itemReceive->user->id == $user->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
//        echo $itemReceive->user->id;
        $trade = new Trade;
        $trade['itemSend'] = $itemSend->id;
        $trade['itemReceive'] = $itemReceive->id;
        $trade['confirmation'] = $itemReceive->user->id;
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
        $user = $request->user()->user;
        $trade = Trade::find($id);

        if ($trade->itemSendObject->user->id == $user->id || $trade->itemReceiveObject->user->id == $user->id)
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
        try {
            $itemSend = Item::where('id','=',$payload->itemSend)->where('user_id','=',$user->id)->firstOrFail();


            $itemReceive = Item::findOrFail($payload->itemReceive);
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }



        //check if the item you are taking is not yours
        if ($itemReceive->user->id == $user->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
        Trade::where('id','=',$id)->where('confirmation', '=', $user->id)->firstOrFail()->update([
            'itemSend' => $itemSend->id,
            'itemReceive' => $itemReceive->id,
            'confirmation' => $itemReceive->user->id
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
        $user = $request->user();
        try {
            $trade = Trade::where('id',$id)->where('confirmation',$user->id)->firstOrFail();
        }
        catch (ModelNotFoundException){
            return response()->json("trade request or confirmation not found", 404);
        }
        //we create new items
        Item::create([
            'name' => $trade->itemSendObject->name,
            'user_id'=> $trade->itemReceiveObject->user->id,
        ]);
        Item::create([
            'name' => $trade->itemReceiveObject->name,
            'user_id'=> $trade->itemSendObject->user->id,
        ]);
        //we delete items so any old connection of trade request to these items will be cascaded
        Item::where('id', $trade->itemSendObject->id)->firstOrFail()->delete();
        Item::where('id', $trade->itemReceiveObject->id)->firstOrFail()->delete();
        $trade->delete();
        return response()->json('request has been confirmed successfully',201);
    }
}
