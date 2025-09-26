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
        Schema::create('transaction_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("doctor_booking_id");
            $table->unsignedBigInteger("transaction_id")->nullable();
            $table->ipAddress("ip")->nullable();
            $table->macAddress('mac')->nullable();
            $table->string("city", 30)->nullable();
            $table->string("country", 30)->nullable();
            $table->string("longitude", 50)->nullable();
            $table->string("latitude", 50)->nullable();
            $table->string("browser", 30)->nullable();
            $table->string("os", 30)->nullable();
            $table->string("timezone", 30)->nullable();
            $table->timestamps();

            $table->foreign("doctor_booking_id")->references("id")->on("doctor_bookings")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_devices');
    }
};
