<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = ['profile_id','confirmation','description','type', 'item1_id'];
    use HasFactory;

    public function senderProfile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function confirmProfile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function item1()
    {
        return $this->belongsTo(Item::class);
    }
    public function item2()
    {
        return $this->belongsTo(Item::class);
    }
}
