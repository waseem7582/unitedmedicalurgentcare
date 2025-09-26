<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("user_id")->nullable();
            $table->unsignedBigInteger("hospital_id")->nullable();
            $table->text('booking_data');
            $table->string('trx_id')->comment('Transaction ID')->nullable();
            $table->integer('booking_exp_seconds')->default(600)->nullable();
            $table->string('date')->comment("Booking Date");
            $table->string('payment_method')->nullable();
            $table->string('slug');
            $table->string('uuid');
            $table->decimal('price',28,8)->default(0);
            $table->text('message')->nullable();
            $table->string('type');
            $table->string('remark')->nullable();
            $table->tinyInteger('status')->comment(' STATUS_SUCCESS= 1,STATUS_PENDING= 2, STATUS_REJECTED= 3')->nullable();

            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("hospital_id")->references("id")->on("hospitals")->onDelete("cascade")->onUpdate("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
