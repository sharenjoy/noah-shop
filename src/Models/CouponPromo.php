<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use Sharenjoy\NoahShop\Models\BasePromo;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class CouponPromo extends BasePromo
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use HasTranslations;
    use HasMediaLibrary;
    use HasSEO;

    protected $table = 'promos';
}
