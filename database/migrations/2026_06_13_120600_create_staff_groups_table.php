<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('staff')->cascadeOnDelete();
            $table->json('days');
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });

        Schema::create('staff_group_helpers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_group_id')->constrained('staff_groups')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_group_helpers');
        Schema::dropIfExists('staff_groups');
    }
};
