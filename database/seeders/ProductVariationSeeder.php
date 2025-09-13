<?php

namespace Database\Seeders;

use App\Models\ProductVariation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductVariationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/productVariations.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $imagePath = $this->storeImage($item['image']);

            ProductVariation::create(
                [
                    'image' => $imagePath,
                    'price' => $item['price'],
                    'sku' => $item['sku'],
                    'stock' => $item['stock'],
                    'product_id' => $item['product_id'],
                    'color_id' => $item['color_id'],
                    'primary_color_id' => $item['primary_color_id'],
                    'secondary_color_id' => $item['secondary_color_id'],
                    'product_type_id' => $item['product_type_id'],
                ]
            );
        }
    }

    private function storeImage($image)
    {
        $imagePath = 'products/' . $image;

        if (File::exists(public_path('images/products/' . $image))) {
            Storage::disk('public')->put($imagePath, File::get(public_path('images/products/' . $image)));
        }

        return Storage::url($imagePath);
    }
}
