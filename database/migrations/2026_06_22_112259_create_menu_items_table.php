<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('subcategory_id')->nullable()->constrained('menu_subcategories')->nullOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedSmallInteger('preparation_time')->nullable();
            $table->boolean('is_available')->default(1)->index();
            $table->boolean('is_featured')->default(0)->index();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_available']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE menu_items ADD CONSTRAINT chk_items_price CHECK (price >= 0)');
            DB::statement('ALTER TABLE menu_items ADD CONSTRAINT chk_items_prep_time CHECK (preparation_time IS NULL OR preparation_time > 0)');
            DB::statement('ALTER TABLE menu_items ADD CONSTRAINT chk_items_is_available CHECK (is_available IN (0, 1))');
            DB::statement('ALTER TABLE menu_items ADD CONSTRAINT chk_items_is_featured CHECK (is_featured IN (0, 1))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
