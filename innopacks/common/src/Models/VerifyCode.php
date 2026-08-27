<?php
namespace InnoShop\Common\Models;

class VerifyCode extends BaseModel
{
    protected $table = 'verify_codes';

    protected $fillable = [
        'account', 'code', 'type', 'deleted_at',
    ];
}
