<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/colors.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Color::create(
                [
                    'name' => $item['name'],
                    'hex_code' => $item['hex_code'],
                ]
            );
        }
    }
}
