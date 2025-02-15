<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Translation;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $batchSize = 5000; // Insert in batches to optimize performance
        $totalRecords = 100000;

        for ($i = 0; $i < $totalRecords / $batchSize; $i++) {
            Translation::factory()->count($batchSize)->create();
        }
    }
}
