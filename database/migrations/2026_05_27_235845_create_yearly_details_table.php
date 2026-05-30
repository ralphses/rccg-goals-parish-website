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
        Schema::create('yearly_details', function (Blueprint $table) {
            $table->id();
            $table->integer('current_year');
            $table->string('year_theme');
            $table->string('year_scripture');
            $table->text('year_scripture_content')->nullable();
            $table->string('current_month');
            $table->string('current_month_theme');
            $table->string('current_month_scripture');
            $table->text('current_month_scripture_content')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yearly_details');
    }
};