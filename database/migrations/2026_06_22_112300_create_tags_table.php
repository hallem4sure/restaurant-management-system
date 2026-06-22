<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('color', 7)->nullable();
            $table->timestamps();
        });
        
        // Optional regex constraint
        // DB::statement("ALTER TABLE tags ADD CONSTRAINT chk_tags_color CHECK (color IS NULL OR color REGEXP '^#[0-9A-Fa-f]{6}$')");
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
