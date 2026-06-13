<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('area', 14, 6)->nullable();
            $table->decimal('avg_waste_kg', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('active');
            $table->foreignId('sector_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
