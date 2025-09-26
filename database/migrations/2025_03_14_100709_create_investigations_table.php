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
        Schema::create('investigations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("hospital_id");
            $table->string("name");
            $table->string("slug");
            $table->string("uuid");
            $table->string("regular_price");
            $table->string("offer_price")->nullable();
            $table->boolean("status")->default(true);
            $table->timestamps();


            $table->foreign("hospital_id")->references("id")->on("hospitals")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investigations');
    }
};
