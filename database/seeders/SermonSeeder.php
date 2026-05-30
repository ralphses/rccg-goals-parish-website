<?php

namespace Database\Seeders;

use App\Models\Sermon;
use App\Models\SermonAttachment;
use Illuminate\Database\Seeder;

class SermonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sermon::factory()
            ->count(20)
            ->has(SermonAttachment::factory()->count(3), 'attachments')
            ->create();
    }
}