<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'city_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('city_id')->nullable();
                $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'city_id')) {
                $table->dropForeign(['city_id']);
                $table->dropColumn('city_id');
            }
        });
    }
};
