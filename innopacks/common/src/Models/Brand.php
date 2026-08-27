<?php
namespace InnoShop\Common\Models;

use Exception;
use InnoShop\Common\Traits\HasPackageFactory;
use InnoShop\Common\Traits\Translatable;

class Brand extends BaseModel
{
    use HasPackageFactory, Translatable;

    protected $table = 'brands';

    protected $fillable = [
        'name', 'slug', 'first', 'logo', 'position', 'active',
    ];

    public $appends = [
        'url',
    ];

    /**
     * Get slug url link.
     *
     * @return string
     * @throws Exception
     */
    public function getUrlAttribute(): string
    {
        if ($this->slug) {
            return front_route('brands.slug_show', ['slug' => $this->slug]);
        }

        return front_route('brands.show', $this);
    }
}
