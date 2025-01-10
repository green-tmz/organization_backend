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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment("Идентификатор пользователя");
            $table->string('first_name')->comment("Имя пользователя");
            $table->string('last_name')->comment("Фамилия пользователя");
            $table->string('email')->unique()->comment("Email пользователя");
            $table->timestamp('email_verified_at')->nullable()->comment("Дата верификации email");
            $table->string('password')->comment("Пароль пользователя");
            $table->rememberToken()->comment("Токен для восстановления пароля");
            $table->timestamps();

            $table->comment("Таблица пользователей");
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary()->comment("Email пользователя");
            $table->string('token')->comment("Токен пользователя");
            $table->timestamp('created_at')->nullable();

            $table->comment("Таблица для восстановления пароля");
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment("Идентификатор сессии");
            $table->foreignId('user_id')->nullable()->index()->comment("Идентификатор пользователя");
            $table->string('ip_address', 45)->nullable()->comment("IP-адрес пользователя");
            $table->text('user_agent')->nullable()->comment("User-Agent пользователя");
            $table->longText('payload')->comment("Данные пользователя");
            $table->integer('last_activity')->index()->comment("Последняя активность пользователя");

            $table->comment("Таблица сессий");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
