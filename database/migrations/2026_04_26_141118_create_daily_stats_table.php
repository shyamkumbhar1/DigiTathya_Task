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
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();

            $table->date('date'); 
        
            $table->integer('total_scans')->default(0);
            $table->integer('total_duplicates')->default(0);
            $table->integer('total_invalid')->default(0);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};
