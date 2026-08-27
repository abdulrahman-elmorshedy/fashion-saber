<?php
namespace InnoShop\Common\Models\Brand;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InnoShop\Common\Models\BaseModel;
use InnoShop\Common\Models\Brand;

class Translation extends BaseModel
{
    protected $table = 'brand_translations';

    protected $fillable = [
        'brand_id', 'locale', 'name', 'summary', 'content', 'meta_title', 'meta_description', 'meta_keywords',
    ];

    /**
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
