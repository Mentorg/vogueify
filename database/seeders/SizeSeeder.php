<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/sizes.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            Size::create([ 'sort_value' => $item['sort_value'], 'product_type_id' => $item['product_type_id'] ]);
        }
    }
}
