<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_schedule_id')->constrained('maintenance_schedules')->cascadeOnDelete();
            $table->date('fecha');
            $table->text('observacion')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('realizado')->default(false);
            $table->timestamps();

            $table->unique(['maintenance_schedule_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedule_days');
    }
};

