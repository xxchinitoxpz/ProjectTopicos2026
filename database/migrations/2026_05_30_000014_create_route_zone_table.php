<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_zone', function (Blueprint $table) {
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
            $table->primary(['route_id', 'zone_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_zone');
    }
};
