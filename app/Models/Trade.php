<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = ['profile_id','confirmation','description','type', 'item1_id'];
    use HasFactory;


    public function item_destination()
    {
        return $this->belongsTo(Item::class, 'item_destination_id', 'id');
    }
    public function item_exchange()
    {
        return $this->belongsTo(Item::class, 'item_exchange_id', 'id');
    }
}
