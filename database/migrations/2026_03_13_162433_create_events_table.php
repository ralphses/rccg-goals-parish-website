<?php

use App\enums\EventStatus;
use App\Models\Event;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->text('description')->nullable();

            $table->timestamp('event_date');

            $table->string('location')->nullable();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->enum('status', [
                EventStatus::ONGOING->value,
                EventStatus::CANCELLED->value,
                EventStatus::COMPLETED->value,
                EventStatus::UPCOMING->value,
            ])->default(EventStatus::UPCOMING->value);

            $table->string('image')->nullable();
            $table->string('video_link')->nullable();
            $table->text('description_heading')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
