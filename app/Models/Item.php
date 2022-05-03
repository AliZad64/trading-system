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

    public function matched1()
    {
        return $this->hasMany(Trade::class,'item_1');
    }

    public function matched2()
    {
        return $this->hasMany(Trade::class,'item_2');
    }

    public function otherItem()
    {
        if($this->matched1->id == $this->id) {
        return $this->matched1;

    }
        return $this->matched2;
    }
}
