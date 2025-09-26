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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("hospital_id");
            $table->string('name');
            $table->string('description',500)->nullable();
            $table->string('uuid');
            $table->string('slug');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('departments');
    }
};
