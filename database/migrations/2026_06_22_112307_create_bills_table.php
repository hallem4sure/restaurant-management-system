<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number', 50)->unique();
            $table->foreignId('order_id')->unique()->constrained('orders')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('service_charge_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'digital_wallet'])->index();
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->timestamp('paid_at')->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['paid_at', 'total_amount']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE bills ADD CONSTRAINT chk_bills_payment_method CHECK (payment_method IN ('cash', 'card', 'digital_wallet'))");
            DB::statement('ALTER TABLE bills ADD CONSTRAINT chk_bills_subtotal CHECK (subtotal >= 0)');
            DB::statement('ALTER TABLE bills ADD CONSTRAINT chk_bills_discount CHECK (discount_amount >= 0)');
            DB::statement('ALTER TABLE bills ADD CONSTRAINT chk_bills_tax CHECK (tax_amount >= 0)');
            DB::statement('ALTER TABLE bills ADD CONSTRAINT chk_bills_service_charge CHECK (service_charge_amount >= 0)');
            DB::statement('ALTER TABLE bills ADD CONSTRAINT chk_bills_total CHECK (total_amount >= 0)');
            DB::statement('ALTER TABLE bills ADD CONSTRAINT chk_bills_amount_paid CHECK (amount_paid >= 0)');
            DB::statement('ALTER TABLE bills ADD CONSTRAINT chk_bills_change CHECK (change_amount >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
