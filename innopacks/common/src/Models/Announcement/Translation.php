<?php
namespace InnoShop\Common\Models\Announcement;

use InnoShop\Common\Models\BaseModel;

class Translation extends BaseModel
{
    protected $table = 'announcement_translations';

    public $timestamps = false;

    protected $fillable = [
        'announcement_id', 'locale', 'text',
    ];
}
