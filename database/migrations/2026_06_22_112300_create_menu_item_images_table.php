<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(0)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['menu_item_id', 'is_primary']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE menu_item_images ADD CONSTRAINT chk_item_images_is_primary CHECK (is_primary IN (0, 1))');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_images');
    }
};
