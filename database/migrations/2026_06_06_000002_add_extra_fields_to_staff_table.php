<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->after('email');
            $table->string('phone', 20)->nullable()->after('birthdate');
            $table->string('address', 255)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['birthdate', 'phone', 'address']);
        });
    }
};
