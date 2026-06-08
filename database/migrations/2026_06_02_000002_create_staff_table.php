<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 15)->unique();
            $table->string('name', 100);
            $table->string('last_name', 150);
            $table->string('email', 150)->unique();
            $table->string('photo')->nullable();
            $table->foreignId('staff_type_id')->constrained('staff_types')->restrictOnDelete();
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
