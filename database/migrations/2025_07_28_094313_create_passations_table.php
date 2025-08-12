<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passations', function (Blueprint $table) {
            $table->id();
            $table->string('nom_patient');
            $table->string('service');
            $table->text('description')->nullable();
            $table->dateTime('date_passation');
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // L'utilisateur (médecin, infirmier, etc.)
            $table->timestamps();
        });
    }

    /**
     * Annule les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('passations');
    }
};
