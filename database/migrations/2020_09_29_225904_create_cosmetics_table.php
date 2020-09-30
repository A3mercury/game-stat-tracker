<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCosmeticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cosmetics', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('identifier');
            $table->string('asset_id');
            $table->string('rarity');
            $table->string('display_name');
            $table->string('local_id');
            $table->string('platform_id');
            $table->string('image')->nullable();
            $table->string('type');
            $table->boolean('is_featured')->nullable();
            $table->integer('price')->nullable();
            $table->string('currency')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cosmetics');
    }
}
