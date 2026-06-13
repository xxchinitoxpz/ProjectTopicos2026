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
            $table->foreignId('driver_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date_start');
            $table->date('date_end');
            $table->json('days');
            $table->string('state', 20)->default('active'); // active, finished
            $table->timestamps();
        });

        Schema::create('planning_helpers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->constrained('plannings')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('planning_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->nullable()->constrained('plannings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action', 50); // created, updated, finished, deleted
            $table->text('details');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planning_changes');
        Schema::dropIfExists('planning_helpers');
        Schema::dropIfExists('plannings');
    }
};
