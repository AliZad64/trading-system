<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeRequest;
use App\Http\Resources\TradeResource;
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
//        $trade = Trade::with(['item_destination'=> function($query) use ($user){
//            $query->where('user_id', '=', $user->id);
//        }])->get();
        $sent_trade = Trade::whereHas('item_destination_id', function ($query) use ($user) {
            $query->where('user_id',$user->id);
        });
        $received_trade = Trade::whereHas('item_exchange_id', function ($query) use ($user) {
            $query->where('user_id',$user->id);
        });
        return response()->json([
            'sent' => $sent_trade,
            'confirm_trade' => $received_trade,

        ],200);

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
            $item_destination = Item::where('id','=',$payload->item_destination_id)->where('user_id','=',$user->id)->firstOrFail();


            $item_exchange = Item::findOrFail($payload->item_exchange_id);
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }
        //check if the item you taking is not yours
        if ($item_exchange->user->id == $user->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
//        echo $item_exchange->user->id;
        $trade = new Trade;
        $trade['item_destination_id'] = $item_destination->id;
        $trade['item_exchange_id'] = $item_exchange->id;
        $trade['confirmation'] = $item_exchange->user->id;
        $trade->save();
        return new TradeResource($trade);

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

        if ($trade->item_destination->user->id == $user->id || $trade->item_exchange->user->id == $user->id)
        {
            return new TradeResource($trade);
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
            $item_destination = Item::where('id','=',$payload->item_destination_id)->where('user_id','=',$user->id)->firstOrFail();


            $item_exchange = Item::findOrFail($payload->item_exchange);
        }
        catch (ModelNotFoundException) {
            return response()->json('item doesnt exist', 404);
        }



        //check if the item you are taking is not yours
        if ($item_exchange->user->id == $user->id)
        {
            return response()->json('dont trade with your own inventory',400);
        }
        Trade::where('id','=',$id)->where('confirmation', '=', $user->id)->firstOrFail()->update([
            'item_destination_id' => $item_destination->id,
            'item_exchange_id' => $item_exchange->id,
            'confirmation' => $item_exchange->user->id
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
            'name' => $trade->item_destination->name,
            'user_id'=> $trade->item_exchange->user->id,
        ]);
        Item::create([
            'name' => $trade->item_exchange->name,
            'user_id'=> $trade->item_destination->user->id,
        ]);
        //we delete items so any old connection of trade request to these items will be cascaded
        Item::where('id', $trade->item_destination->id)->firstOrFail()->delete();
        Item::where('id', $trade->item_exchange->id)->firstOrFail()->delete();
        $trade->delete();
        return response()->json('request has been confirmed successfully',201);
    }
}
