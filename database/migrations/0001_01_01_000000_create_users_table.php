<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->enum('role', [
                UserRole::ADMIN->value,
                UserRole::EDITOR->value,
                UserRole::PASTOR->value,
                UserRole::MEMBER->value,
                UserRole::MEDIA->value,
            ])->default(UserRole::EDITOR->value);

            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();

            $table->enum('status', [
                UserStatus::ACTIVE->value,
                UserStatus::CREATED->value,
                UserStatus::SUSPENDED->value
            ])->default(UserStatus::CREATED->value);

            $table->boolean('must_change_password')->default(true);
            $table->timestamp('last_login_at')->nullable();

            // New Fields
            $table->string('address')->nullable();
            $table->date('day_joined')->nullable();
            $table->text('what_attracted_you')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('occupation')->nullable();
            $table->text('hobbies')->nullable();
            $table->text('favourite_quote')->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('can_login')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};