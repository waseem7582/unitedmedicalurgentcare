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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("hospital_id");
            $table->unsignedBigInteger("branch_id");
            $table->unsignedBigInteger("department_id");
            $table->string('name');
            $table->string('image');
            $table->string('title');
            $table->string('qualification');
            $table->string('specialty');
            $table->string('language');
            $table->string('designation');
            $table->string('contact');
            $table->string('off_days');
            $table->integer('floor_number');
            $table->integer('room_number');
            $table->string('address');
            $table->string('fees');
            $table->string('slug');
            $table->boolean("status")->comment('STATUS_SUCCESS = 1,STATUS_PENDING = 2,STATUS_REJECTED = 3');
            $table->timestamps();

            $table->foreign("branch_id")->references("id")->on("branches")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("hospital_id")->references("id")->on("hospitals")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("department_id")->references("id")->on("departments")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
    }
};
