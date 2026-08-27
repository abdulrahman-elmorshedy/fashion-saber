<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use InnoShop\Common\Models\Locale;

class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        $items = $this->getLocales();
        if ($items) {
            Locale::query()->truncate();
            foreach ($items as $item) {
                Locale::query()->create($item);
            }
        }
    }

    private function getLocales(): array
    {
        return [
            [
                'name'     => 'English',
                'code'     => 'en',
                'image'    => 'images/flag/en.png',
                'position' => 0,
                'active'   => 1,
            ],
            [
                'name'     => 'العربية',
                'code'     => 'ar',
                'image'    => 'images/flag/ar.png',
                'position' => 1,
                'active'   => 1,
            ],
        ];
    }
}
