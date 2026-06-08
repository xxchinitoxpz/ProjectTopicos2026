<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('plate', 20)->unique();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedSmallInteger('occupant_capacity')->nullable();
            $table->decimal('load_capacity', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->foreignId('model_id')->constrained('brand_models')->restrictOnDelete();
            $table->foreignId('type_id')->constrained('vehicle_types')->restrictOnDelete();
            $table->foreignId('color_id')->constrained('vehicle_colors')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
