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
        Schema::create('prueba', function (Blueprint $table) {
            $table->id();
            $table->string('name',200);
            $table->string('cuenta','30');
            $table->string('mensaje','250');
            $table->string('bank_code','4');
            $table->string('cod_response','4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
