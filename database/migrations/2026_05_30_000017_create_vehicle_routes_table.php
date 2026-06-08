<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_routes', function (Blueprint $table) {
            $table->id();
            $table->date('date_route');
            $table->time('time_route');
            $table->text('description')->nullable();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_routes');
    }
};
