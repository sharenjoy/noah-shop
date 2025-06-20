<?php

namespace Sharenjoy\NoahShop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sharenjoy\NoahShop\Enums\InvoiceHolderType;
use Sharenjoy\NoahShop\Enums\InvoiceType;
use Sharenjoy\NoahShop\Models\InvoicePrice;
use Sharenjoy\NoahShop\Models\Order;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use CommonModelTrait;
    use LogsActivity;

    protected $casts = [
        'type' => InvoiceType::class,
        'holder_type' => InvoiceHolderType::class,
    ];

    protected array $sort = [
        'created_at' => 'desc',
    ];

    protected function formFields(): array
    {
        return [
            'left' => [
                'title' => [
                    'slug' => true,
                    'required' => true,
                    'rules' => ['required', 'string'],
                    // 'localeRules' => [
                    //     'zh_TW' => ['required', 'string', 'max:255'],
                    //     'en' => ['required', 'string', 'max:255'],
                    // ],
                ],
                'slug' => ['maxLength' => 50, 'required' => true],
                'categories' => ['required' => true],
                'tags' => ['max' => 5, 'multiple' => true],
                'description' => ['required' => true, 'rules' => ['required', 'string']],
                'content' => [
                    'profile' => 'simple',
                    'required' => true,
                    'rules' => ['required'],
                ],
            ],
            'right' => [
                'img' => ['required' => true],
                'album' => ['required' => true],
                'is_active' => ['required' => true],
                'published_at' => ['required' => true],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'title' => ['description' => true],
            'slug' => [],
            'categories' => [],
            'tags' => ['tagType' => 'product'],
            'thumbnail' => [],
            'seo' => [],
            'is_active' => [],
            'published_at' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(InvoicePrice::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** OTHERS */
}
