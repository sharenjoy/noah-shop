<?php

namespace Sharenjoy\NoahShop\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Sharenjoy\NoahShop\Models\Promo;

trait HasPromos
{
    public function promos(): MorphToMany
    {
        return $this->morphToMany(Promo::class, 'promoable');
    }
}
