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
        Schema::create('cota_especiais', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('consumidor_codpes')->unique();
            $table->foreign('consumidor_codpes')->references('codpes')->on('consumidores')->onDelete('cascade');
            $table->unsignedInteger('valor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cota_especiais');
    }
};
