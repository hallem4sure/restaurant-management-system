<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('restaurant_tables')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_email')->nullable();
            $table->unsignedTinyInteger('party_size');
            $table->enum('type', ['immediate', 'scheduled']);
            $table->dateTime('reserved_at')->index();
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->enum('status', ['pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'])->default('pending')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['table_id', 'reserved_at', 'status']);
            $table->index(['status', 'reserved_at']);
        });

        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_party_size CHECK (party_size >= 1 AND party_size <= 50)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_duration CHECK (duration_minutes >= 15 AND duration_minutes <= 480)');
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservations_type CHECK (type IN ('immediate', 'scheduled'))");
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservations_status CHECK (status IN ('pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
