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
            $table->foreignIdFor(\App\Models\Profile::class, 'confirmation');
            $table->foreignIdFor(\App\Models\Item::class, 'itemSend');
            $table->foreignIdFor(\App\Models\Item::class, 'itemReceive ');
            $table->text('description')->nullable();
            $table->enum('type',['waiting','success','failed'])->default('waiting');
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
