<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Giftproduct extends Model implements Sortable
{
    use CommonModelTrait;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;
    use HasTranslations;
    use HasMediaLibrary;

    protected $casts = [
        'album' => 'array',
        'is_active' => 'boolean',
    ];

    public $translatable = [
        'title',
        'description',
        'content',
    ];

    protected array $sort = [
        'order_column' => 'desc',
    ];

    protected function formFields(): array
    {
        return [
            'left' => [
                'title' => [
                    'slug' => true,
                    'required' => true,
                    'rules' => ['required', 'string'],
                ],
                'slug' => ['maxLength' => 50, 'required' => true],
                'description' => ['required' => true, 'rules' => ['required', 'string']],
                'content' => ['profile' => 'simple'],
            ],
            'right' => [
                'img' => ['required' => true],
                'album' => [],
                'is_active' => ['required' => true],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'thumbnail' => [],
            'title' => ['description' => true],
            'productSpecification.spec_detail_name' => ['alias' => 'belongs_to', 'label' => 'specification', 'relation' => 'productSpecification'],
            'slug' => [],
            'is_active' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function productSpecification(): BelongsTo
    {
        return $this->belongsTo(ProductSpecification::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** SEO */

    /** OTHERS */

    public function getReplicateAction($type)
    {
        $resourceName = 'giftproducts';
        $classname = $type == 'table' ? '\Filament\Tables\Actions\ReplicateAction' : '\Filament\Actions\ReplicateAction';
        return $classname::make()
            ->after(function (Model $replica): void {
                $replica->slug = $replica->slug . '-' . $replica->id;
                $replica->save();
            })
            ->successRedirectUrl(function (Model $replica) use ($resourceName): string {
                $currentPanelId = Filament::getCurrentPanel()->getId();
                return route('filament.' . $currentPanelId . '.resources.' . $resourceName . '.edit', [
                    'record' => $replica,
                ]);
            });
    }
}
