<?php
use Illuminate\Database\Migrations\Migration;
use InnoShop\Common\Models\Brand;

return new class extends Migration
{
    public function up(): void
    {
        $defaultLocale = setting_locale_code();
        $brands        = Brand::all();

        foreach ($brands as $brand) {
            if (empty($brand->name)) {
                continue;
            }

            // Check if translation already exists
            $exists = $brand->translations()->where('locale', $defaultLocale)->exists();
            if ($exists) {
                continue;
            }

            $brand->translations()->create([
                'locale' => $defaultLocale,
                'name'   => $brand->name,
            ]);
        }
    }

    public function down(): void
    {
        // No need to reverse the seed
    }
};
