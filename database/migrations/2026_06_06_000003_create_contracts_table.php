<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_type', 30); // permanente, nombrado, temporal
            $table->date('date_start');
            $table->date('date_end')->nullable();
            $table->decimal('salary', 10, 2);
            $table->integer('probation')->nullable(); // en meses
            $table->string('state', 20)->default('active'); // active, inactive, expired
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
