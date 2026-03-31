<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        $foreignKeys = DB::select('PRAGMA foreign_key_list(users)');
        $hasLegacyCountryReference = collect($foreignKeys)
            ->contains(fn (object $foreignKey): bool => $foreignKey->from === 'country_id' && $foreignKey->table === 'countries_legacy_backup');

        if (! $hasLegacyCountryReference) {
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');

        Schema::create('users_rebuilt', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('role')->default('user');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_banned')->default(false);
            $table->string('phone')->nullable();
            $table->string('national_id')->nullable();
            $table->foreignId('created_by_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('gender')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('banned_at')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
        });

        DB::statement('
            INSERT INTO users_rebuilt (
                id, name, email, email_verified_at, password, image, role, is_active, is_banned, phone,
                national_id, created_by_manager_id, gender, approved_at, approved_by, banned_at, banned_by,
                remember_token, deleted_at, created_at, updated_at, country_id, two_factor_secret,
                two_factor_recovery_codes, two_factor_confirmed_at, last_login_at, city_id
            )
            SELECT
                id, name, email, email_verified_at, password, image, role, is_active, is_banned, phone,
                national_id, created_by_manager_id, gender, approved_at, approved_by, banned_at, banned_by,
                remember_token, deleted_at, created_at, updated_at, country_id, two_factor_secret,
                two_factor_recovery_codes, two_factor_confirmed_at, last_login_at, city_id
            FROM users
        ');

        Schema::drop('users');
        Schema::rename('users_rebuilt', 'users');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // No-op. This repair migration only fixes an invalid SQLite foreign key target.
    }
};
