<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/categories.json');
        $data = json_decode($json, true);

        foreach ($data as $item) {
            $imagePath = $this->storeImage($item['image']);

            ProductCategory::create(
                [
                    'name' => $item['name'],
                    'image' => $imagePath,
                ]
            );
        }
    }

    private function storeImage($image)
    {
        $imagePath = 'categories/' . $image;

        if (File::exists(public_path('images/categories/' . $image))) {
            Storage::disk('public')->put($imagePath, File::get(public_path('images/categories/' . $image)));
        }

        return Storage::url($imagePath);
    }
}
