<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    public $fillable = ['user_id','country_id','birthday','age','balance'];
    use HasFactory;

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function item()
    {
        return $this->hasMany(Item::class);
    }
}
