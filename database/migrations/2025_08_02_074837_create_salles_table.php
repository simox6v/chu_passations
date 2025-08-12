<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('salles', function (Blueprint $table) {
            $table->id(); // unsignedBigInteger by default
            $table->string('nom');
            $table->integer('nombre_lits');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('salles');
    }
};
