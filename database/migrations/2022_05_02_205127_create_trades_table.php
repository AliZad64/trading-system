<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class, 'confirmation_id');
            $table->foreignIdFor(\App\Models\Item::class, 'item_destination_id');
            $table->foreignIdFor(\App\Models\Item::class, 'item_exchange_id');
            $table->foreignIdFor(\App\Models\Status::class,'status_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trades');
    }
};
