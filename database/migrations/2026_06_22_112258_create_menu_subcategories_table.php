<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE menu_subcategories ADD CONSTRAINT chk_subcategories_is_active CHECK (is_active IN (0, 1))');
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_subcategories');
    }
};
