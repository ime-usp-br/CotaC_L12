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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->index('consumidor_codpes'); // For FK lookups and JOINs
            $table->index('estado');             // For WHERE filters
            $table->index('created_at');         // For date range queries and ORDER BY
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropIndex(['consumidor_codpes']);
            $table->dropIndex(['estado']);
            $table->dropIndex(['created_at']);
        });
    }
};
