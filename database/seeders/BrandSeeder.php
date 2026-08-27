<?php
namespace Database\Seeders;

use InnoShop\Common\Models\Brand;

class BrandSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getBrands();
        if ($items) {
            $this->safeTruncate(Brand::class);

            foreach ($items as $item) {
                Brand::query()->create($item);
            }
        }
    }

    private function getBrands(): array
    {
        return [
            [
                'name'     => 'Adidas',
                'slug'     => 'adidas',
                'first'    => 'A',
                'logo'     => 'images/demo/brands/adidas.png',
                'position' => 0,
                'active'   => true,
            ],
            [
                'name'     => 'Nike',
                'slug'     => 'nike',
                'first'    => 'N',
                'logo'     => 'images/demo/brands/nike.png',
                'position' => 1,
                'active'   => true,
            ],
        ];
    }
}
