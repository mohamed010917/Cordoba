<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("room_id")->constrained()->onDelete("cascade");
            $table->integer("accompany_number");
            $table->bigInteger("paid_price_cents");
            $table->foreignId("receptionist_id")->nullable()->constrained("users")->
            onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
