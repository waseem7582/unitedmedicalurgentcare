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
        Schema::create('investigation_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("investigation_id");
            $table->unsignedBigInteger("investigation_category_id");
            $table->timestamps();

            $table->foreign("investigation_id")->references("id")->on("investigations")->onDelete("cascade")->onUpdate("cascade");
            $table->foreign("investigation_category_id")->references("id")->on("investigation_categories")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('investigation_has_categories');
    }
};
