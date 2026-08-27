<?php
namespace InnoShop\Common\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use InnoShop\Common\Models\Region\State;

class Region extends BaseModel
{
    protected $table = 'regions';

    protected $fillable = [
        'name', 'description', 'position', 'active',
    ];

    /**
     * @return HasMany
     */
    public function regionStates(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
