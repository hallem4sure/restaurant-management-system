<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group', 100)->nullable()->index();
            $table->string('label')->nullable();
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'json'])->default('string');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE settings ADD CONSTRAINT chk_settings_type CHECK (type IN ('string', 'integer', 'float', 'boolean', 'json'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
