<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('room_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('hour_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('scheduled');
            $table->date('date');
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
        });

        DB::statement(
            "CREATE UNIQUE INDEX schedules_active_slot_unique
            ON schedules (room_id, date, hour_id) WHERE status = 'scheduled'"
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropar a tabela remove também o índice único parcial.
        Schema::dropIfExists('schedules');
    }
};
