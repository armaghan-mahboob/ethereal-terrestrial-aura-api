<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImportSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/product_export.csv');
        $csv = file($csvPath);
        array_shift($csv);

        $count = 0;

        foreach ($csv as $line) {
            $row = str_getcsv($line);
            if (!$row || count($row) < 17) {
                continue;
            }

            [
                $name,
                $description,
                $category,
                $subcategory,
                $price,
                $imagesJson,
                $stockQuantity,
                $inStock,
                $isNewArrival,
                $sizesJson,
                $colorsJson,
                $featured,
                $sortOrder,
                $id,
                $createdDate,
                $updatedDate,
                $createdById,
                $isSample,
            ] = $row;

            $imagesJson = str_replace('""', '"', $imagesJson);
            $sizesJson = str_replace('""', '"', $sizesJson);
            $colorsJson = str_replace('""', '"', $colorsJson);

            $imageUrls = json_decode($imagesJson, true) ?? [];
            $localImages = [];
            foreach ($imageUrls as $url) {
                $filename = basename($url);
                if (file_exists(storage_path('app/public/products/' . $filename))) {
                    $localImages[] = 'products/' . $filename;
                } else {
                    $localImages[] = $url;
                }
            }

            $sizes = json_decode($sizesJson, true) ?? [];
            $colors = json_decode($colorsJson, true) ?? [];

            $inStockBool = filter_var($inStock, FILTER_VALIDATE_BOOLEAN);
            $isNewArrivalBool = filter_var($isNewArrival, FILTER_VALIDATE_BOOLEAN);
            $featuredBool = filter_var($featured, FILTER_VALIDATE_BOOLEAN);

            $sortOrderInt = $sortOrder !== '' ? (int) $sortOrder : null;
            $subcategoryNull = $subcategory !== '' ? $subcategory : null;

            $createdAt = $createdDate ?: now();
            $updatedAt = $updatedDate ?: now();

            DB::table('products')->insert([
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'category' => $category,
                'subcategory' => $subcategoryNull,
                'price' => (float) $price,
                'images' => json_encode($localImages),
                'stock_quantity' => (int) $stockQuantity,
                'in_stock' => $inStockBool,
                'is_new_arrival' => $isNewArrivalBool,
                'sizes' => json_encode($sizes),
                'colors' => json_encode($colors),
                'featured' => $featuredBool,
                'sort_order' => $sortOrderInt,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            $count++;
        }

        echo "Imported {$count} products into local database.\n";
    }
}
