<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('city')->comment("Город");
            $table->string('street')->comment("Улица");
            $table->string('office')->nullable()->comment("Офис");
            $table->magellanPoint('location', 4326)
                ->nullable()
                ->comment("Локация здания");
            $table->timestamps();

            $table->comment("Таблица зданий");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
