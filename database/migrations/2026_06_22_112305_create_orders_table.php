<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('table_id')->nullable()->constrained('restaurant_tables')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('waiter_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete()->cascadeOnUpdate();
            $table->enum('type', ['walk_in', 'reservation'])->default('walk_in')->index();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'])->default('pending')->index();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('service_charge_rate', 5, 2)->default(0);
            $table->decimal('service_charge_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('special_instructions')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'status']);
            $table->index(['table_id', 'status']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders ADD CONSTRAINT chk_orders_type CHECK (type IN ('walk_in', 'reservation'))");
            DB::statement("ALTER TABLE orders ADD CONSTRAINT chk_orders_status CHECK (status IN ('pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'))");
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_subtotal CHECK (subtotal >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_discount CHECK (discount_amount >= 0)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_tax_rate CHECK (tax_rate >= 0 AND tax_rate <= 100)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_service_rate CHECK (service_charge_rate >= 0 AND service_charge_rate <= 100)');
            DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_total CHECK (total_amount >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
