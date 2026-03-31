<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('countries')) {
            return;
        }

        if (Schema::hasColumn('countries', 'iso2')) {
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');

        if (Schema::hasTable('countries_legacy_backup')) {
            Schema::drop('countries_legacy_backup');
        }

        Schema::rename('countries', 'countries_legacy_backup');

        Schema::create('countries', function (Blueprint $table): void {
            $table->id();
            $table->string('iso2', 2);
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->string('phone_code', 5);
            $table->string('iso3', 3);
            $table->string('region');
            $table->string('subregion');
            $table->string('native')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('emoji')->nullable();
            $table->string('emojiU')->nullable();
        });

        Schema::drop('countries_legacy_backup');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        if (! Schema::hasTable('countries')) {
            return;
        }

        if (Schema::hasColumn('countries', 'code')) {
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');

        if (Schema::hasTable('countries_world_backup')) {
            Schema::drop('countries_world_backup');
        }

        Schema::rename('countries', 'countries_world_backup');

        Schema::create('countries', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::drop('countries_world_backup');

        DB::statement('PRAGMA foreign_keys = ON');
    }
};
