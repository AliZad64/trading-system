<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public $fillable = ['profile_id','name','itemdetail','description'];
    use HasFactory;

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function sendTrade()
    {
        return $this->hasMany(Trade::class,'itemSend');
    }

    public function receiveTrade()
    {
        return $this->hasMany(Trade::class,'itemReceive');
    }

    public function allTrade()
    {
        if($this->sendTrade->id == $this->id) {
        return $this->sendTrade;

    }
        return $this->receiveTrade;
    }
}
