<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/products.json');

        $data = json_decode($json, true);

        foreach ($data as $item) {
            Product::create(
                [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'features' => $item['features'],
                    'gender' => $item['gender'],
                    'category_id' => $item['category_id']
                ]
            );
        }
    }
}
