<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'description', 'category', 'subcategory', 'price', 'images',
        'stock_quantity', 'in_stock', 'is_new_arrival', 'sizes', 'colors', 'featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'images' => 'array',
            'sizes' => 'array',
            'colors' => 'array',
            'in_stock' => 'boolean',
            'is_new_arrival' => 'boolean',
            'featured' => 'boolean',
        ];
    }

    protected $appends = ['created_date'];

    public function getCreatedDateAttribute(): ?string
    {
        return $this->created_at?->toISOString();
    }

    public function getImagesAttribute(mixed $value): array
    {
        $images = json_decode($value ?? '[]', true) ?: [];

        return array_map(
            fn (string $path) => str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path),
            $images,
        );
    }
}
