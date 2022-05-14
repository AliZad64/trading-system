<?php

namespace App\Models;

use HusseinFeras\Laraimage\Traits\SingleColumnSingleImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use SingleColumnSingleImage;

    public $imageColumn = 'image';

    protected $casts = [
        'image' => 'json'
    ];

    public $fillable = ['user_id','name','itemdetail','description'];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trade_destination()
    {
        return $this->hasMany(Trade::class,'item_destination_id');
    }

    public function trade_exchange()
    {
        return $this->hasMany(Trade::class,'item_exchange_id');
    }

    public function allTrade()
    {
        if($this->sendTrade->id == $this->id) {
        return $this->sendTrade;

    }
        return $this->receiveTrade;
    }

    public function imagesPath()
    {
        return 'categories/images/'.$this->id;
    }
}
