<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = ['profile_id','confirmation','description','type', 'item1_id'];
    use HasFactory;


    public function itemSendObject()
    {
        return $this->belongsTo(Item::class, 'itemSend', 'id');
    }
    public function itemReceiveObject()
    {
        return $this->belongsTo(Item::class, 'itemReceive', 'id');
    }
}
