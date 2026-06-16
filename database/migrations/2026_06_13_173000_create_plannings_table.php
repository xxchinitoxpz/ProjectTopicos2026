<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_group_id')->constrained('staff_groups')->cascadeOnDelete();
            $table->date('date_start');
            $table->date('date_end');
            $table->json('days');
            $table->timestamps();
        });

        Schema::create('planning_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->constrained('plannings')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('staff')->cascadeOnDelete();
            $table->string('state', 20)->default('active'); // active, finished, reprogramado
            $table->timestamps();
        });

        Schema::create('planning_day_helpers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_day_id')->constrained('planning_days')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('planning_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_day_id')->constrained('planning_days')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('change_type', 50); // turno, vehiculo, personal
            $table->string('old_value', 150);
            $table->string('new_value', 150);
            $table->string('reason_type', 150); // motive
            $table->text('details')->nullable(); // description
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_changes');
        Schema::dropIfExists('planning_day_helpers');
        Schema::dropIfExists('planning_days');
        Schema::dropIfExists('plannings');
    }
};
