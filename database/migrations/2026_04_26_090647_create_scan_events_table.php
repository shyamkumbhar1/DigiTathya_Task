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
        Schema::create('scan_events', function (Blueprint $table) {
            $table->id();
            $table->string('scan_id');
            $table->string('session_id')->nullable();
            $table->string('operator_id')->nullable();
            $table->string('partner_id')->nullable();
            $table->string('device_id')->nullable();
        
            $table->string('action');
        
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
        
            $table->timestamp('device_timestamp')->nullable();
        
            $table->timestamps();
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_events');
    }
};
