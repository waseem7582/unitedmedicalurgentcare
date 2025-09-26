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
        Schema::create('hospital_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("hospital_id");
            $table->string("method")->nullable();
            $table->string("subject",255);
            $table->text("message",3000);
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
        Schema::dropIfExists('hospital_mail_logs');
    }
};
