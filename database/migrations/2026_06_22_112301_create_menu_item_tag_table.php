<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_item_tag', function (Blueprint $table) {
            $table->foreignId('menu_item_id')->constrained('menu_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete()->cascadeOnUpdate();
            
            $table->primary(['menu_item_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_tag');
    }
};
