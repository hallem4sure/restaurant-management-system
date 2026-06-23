<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(1)->index();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->index();
            $table->json('applicable_days')->nullable();
            $table->time('applicable_from_time')->nullable();
            $table->time('applicable_to_time')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE offers ADD CONSTRAINT chk_offers_type CHECK (type IN ('percentage', 'fixed'))");
            DB::statement('ALTER TABLE offers ADD CONSTRAINT chk_offers_value_positive CHECK (value > 0)');
            DB::statement("ALTER TABLE offers ADD CONSTRAINT chk_offers_percentage_max CHECK (type != 'percentage' OR value <= 100)");
            DB::statement('ALTER TABLE offers ADD CONSTRAINT chk_offers_min_order CHECK (min_order_amount IS NULL OR min_order_amount >= 0)');
            DB::statement('ALTER TABLE offers ADD CONSTRAINT chk_offers_max_discount CHECK (max_discount_amount IS NULL OR max_discount_amount > 0)');
            DB::statement('ALTER TABLE offers ADD CONSTRAINT chk_offers_date_range CHECK (ends_at > starts_at)');
            DB::statement('ALTER TABLE offers ADD CONSTRAINT chk_offers_time_range CHECK ((applicable_from_time IS NULL AND applicable_to_time IS NULL) OR (applicable_from_time IS NOT NULL AND applicable_to_time IS NOT NULL AND applicable_to_time > applicable_from_time))');
            DB::statement('ALTER TABLE offers ADD CONSTRAINT chk_offers_is_active CHECK (is_active IN (0, 1))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
