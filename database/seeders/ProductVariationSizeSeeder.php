<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductVariationSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/productVariationSize.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            DB::table('product_variation_size')->insert([
                'stock' => $item['stock'],
                'product_variation_id' => $item['product_variation_id'],
                'size_id' => $item['size_id'],
            ]);
        }
    }
}
