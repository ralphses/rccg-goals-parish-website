<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('birthday_board_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('celebration_date')->index();
            $table->text('message');
            $table->timestamps();

            $table->index(['celebration_date', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('birthday_board_comments');
    }
};
