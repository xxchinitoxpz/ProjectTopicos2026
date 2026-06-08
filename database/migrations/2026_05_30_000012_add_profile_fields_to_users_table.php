<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('dni', 15)->nullable()->unique()->after('name');
            $table->date('birthdate')->nullable()->after('dni');
            $table->string('license', 30)->nullable()->after('birthdate');
            $table->string('address')->nullable()->after('license');
            $table->foreignId('user_type_id')->nullable()->after('address')->constrained()->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->after('user_type_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_type_id');
            $table->dropConstrainedForeignId('zone_id');
            $table->dropColumn(['dni', 'birthdate', 'license', 'address']);
        });
    }
};
