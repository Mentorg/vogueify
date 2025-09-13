<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/productTypes.json');
        $data = json_decode($json, true);

        foreach($data as $item) {
            ProductType::create(
                [
                    'type' => $item['type'],
                    'label' => $item['label'],
                    'category_id' => $item['category_id'],
                ]
            );
        }
    }
}
