<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('menu_item_id')->constrained('menu_items')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete()->cascadeOnUpdate();
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->enum('kitchen_status', ['pending', 'preparing', 'ready', 'served'])->default('pending')->index();
            $table->timestamps();

            $table->index(['order_id', 'kitchen_status']);
            $table->index(['menu_item_id', 'created_at']);
        });

        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_items_quantity CHECK (quantity >= 1 AND quantity <= 99)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_items_unit_price CHECK (unit_price >= 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_items_discount CHECK (discount_amount >= 0)');
        DB::statement('ALTER TABLE order_items ADD CONSTRAINT chk_order_items_subtotal CHECK (subtotal >= 0)');
        DB::statement("ALTER TABLE order_items ADD CONSTRAINT chk_order_items_kitchen_status CHECK (kitchen_status IN ('pending', 'preparing', 'ready', 'served'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
