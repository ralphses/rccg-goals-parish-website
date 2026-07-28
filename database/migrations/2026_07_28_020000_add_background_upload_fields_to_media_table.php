<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
            $table->string('upload_status')->default('ready')->after('youtube_source_path');
            $table->text('upload_last_error')->nullable()->after('upload_status');
            $table->timestamp('upload_queued_at')->nullable()->after('upload_last_error');
            $table->timestamp('upload_completed_at')->nullable()->after('upload_queued_at');
        });

        DB::table('media')->update([
            'upload_status' => 'ready',
            'upload_completed_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('media')
            ->whereNull('file_path')
            ->update([
                'file_path' => '',
            ]);

        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn([
                'upload_status',
                'upload_last_error',
                'upload_queued_at',
                'upload_completed_at',
            ]);
            $table->string('file_path')->nullable(false)->change();
        });
    }
};
