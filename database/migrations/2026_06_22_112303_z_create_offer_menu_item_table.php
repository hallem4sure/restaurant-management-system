<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_menu_item', function (Blueprint $table) {
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete()->cascadeOnUpdate();
            
            $table->primary(['offer_id', 'menu_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_menu_item');
    }
};
