<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vacations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->date('date_request');
            $table->date('date_start');
            $table->date('date_end');
            $table->integer('days_requested');
            $table->text('notes')->nullable();
            $table->string('state', 20)->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
