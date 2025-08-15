<?php

namespace Database\Seeders;

use App\Models\Quest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'history',
            'teachers',
            'library',
            'cinema',
            'birds',
            'markers',
            'roles',
            'formula',
            'students',
            'achievements',
            'person',
            'secret',
        ];

        foreach ($names as $name) {
            Quest::firstOrCreate(['name' => $name]);
        }
    }
}
