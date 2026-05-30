<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Testimony;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonySeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimony::factory()
            ->count(20)
            ->create()
            ->each(function ($testimony) {
                Media::factory()
                    ->count(rand(1, 3))
                    ->create([
                        'mediable_id' => $testimony->id,
                        'mediable_type' => get_class($testimony),
                    ]);
            });
    }
}