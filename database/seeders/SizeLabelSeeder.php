<?php

namespace Database\Seeders;

use App\Models\SizeLabel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SizeLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/sizeLabels.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            SizeLabel::create(
                [
                    'label' => $item['label'],
                    'system' => $item['system'],
                    'unit' => $item['unit'],
                    'gender' => $item['gender'],
                    'size_id' => $item['size_id']
                ]
            );
        }
    }
}
