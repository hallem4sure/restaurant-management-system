<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number', 50)->unique();
            $table->unsignedTinyInteger('capacity')->index();
            $table->enum('type', ['public', 'private'])->default('public')->index();
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available')->index();
            $table->string('location')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('qr_code_token', 64)->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'capacity']);
            $table->index(['type', 'status']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE restaurant_tables ADD CONSTRAINT chk_tables_capacity CHECK (capacity >= 1 AND capacity <= 50)');
            DB::statement("ALTER TABLE restaurant_tables ADD CONSTRAINT chk_tables_type CHECK (type IN ('public', 'private'))");
            DB::statement("ALTER TABLE restaurant_tables ADD CONSTRAINT chk_tables_status CHECK (status IN ('available', 'occupied', 'reserved', 'maintenance'))");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
