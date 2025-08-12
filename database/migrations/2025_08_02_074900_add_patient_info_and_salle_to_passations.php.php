<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('passations', function (Blueprint $table) {
            $table->string('prenom')->nullable();
            $table->string('cin')->nullable();
            $table->string('ip')->nullable();

            $table->unsignedBigInteger('salle_id')->nullable();

            // Fix: Use `constrained` only if table exists!
            $table->foreign('salle_id')->references('id')->on('salles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('passations', function (Blueprint $table) {
            // First drop foreign key, then column
            $table->dropForeign(['salle_id']);
            $table->dropColumn('salle_id');

            $table->dropColumn(['prenom', 'cin', 'ip', 'description']);
        });
    }
};
