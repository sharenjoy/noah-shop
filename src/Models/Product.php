<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Sharenjoy\NoahShop\Enums\DeliveryProvider;
use Sharenjoy\NoahShop\Enums\DeliveryType;
use Sharenjoy\NoahShop\Enums\ProductLimit;
use Sharenjoy\NoahShop\Enums\StockMethod;
use Sharenjoy\NoahShop\Models\Brand;
use Sharenjoy\NoahShop\Models\ProductSpecification;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasCategoryTree;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Sharenjoy\NoahShop\Models\Traits\HasPromos;
use Sharenjoy\NoahCms\Models\Traits\HasTags;
use Sharenjoy\NoahCms\Utils\JsonLD;
use Sharenjoy\NoahCms\Utils\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements Sortable
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use SortableTrait;
    use HasTranslations;
    use HasMediaLibrary;
    use HasCategoryTree;
    use HasTags;
    use HasSEO;

    protected $casts = [
        'album' => 'array',
        'categories' => 'array',
        'specs' => 'json',
        'stock_method' => 'json',
        'product_limit' => 'json',
        'is_single_spec' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public $translatable = [
        'title',
        'description',
        'content',
    ];

    protected array $sort = [
        'published_at' => 'desc',
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
                'check' => Section::make()->schema([
                    'provider' => CheckboxList::make('product_limit')
                        ->options(DeliveryProvider::visibleOptions())
                        ->label(__('noah-cms::noah-cms.delivery_provider_limit')),
                    'delivery' => CheckboxList::make('product_limit')
                        ->options(DeliveryType::visibleOptions())
                        ->label(__('noah-cms::noah-cms.delivery_type_limit')),
                    'product' => CheckboxList::make('product_limit')
                        ->options(ProductLimit::visibleOptions())
                        ->label(__('noah-cms::noah-cms.product_limit')),
                ])->columns(3),
                'stock' => Section::make()->schema([
                    'stock_method' => CheckboxList::make('stock_method')
                        ->options(StockMethod::visibleOptions())
                        ->label(__('noah-cms::noah-cms.stock_method')),
                ])->columns(3),
                'is_single_spec' => ['alias' => 'yes_no', 'required' => true, 'disable' => 'edit', 'live' => true],
                'specs' => Section::make()->schema([
                    Placeholder::make(__('noah-cms::noah-cms.single_spec_selected'))
                        ->visible(fn(Get $get): bool => $get('is_single_spec') == true),
                    Repeater::make('specs')
                        ->label(__('noah-cms::noah-cms.specification'))
                        ->schema([
                            TextInput::make('spec_name')->label(__('noah-cms::noah-cms.spec_name'))->required(),
                            Repeater::make('spec_details')
                                ->label(__('noah-cms::noah-cms.spec_details'))
                                ->schema([
                                    TextInput::make('detail_name')->label(__('noah-cms::noah-cms.spec_detail_name'))->required(),
                                ])
                                ->columns(1)
                        ])
                        ->disabled(function (string $operation) {
                            return $operation === 'edit' ? true : false;
                        })
                        ->columns(1)
                        ->collapsible()
                        ->maxItems(3)
                        ->visible(fn(Get $get): bool => $get('is_single_spec') == false)
                ])->visible(fn($operation) => $operation === 'create'),
                'content' => [
                    'profile' => 'simple',
                ],
            ],
            'right' => [
                'img' => [],
                'album' => [],
                'is_active' => ['required' => true],
                'published_at' => ['required' => true],
                'brand_id' => ['alias' => 'belongs_to', 'relation' => 'brand'],
                'categories' => ['required' => true],
                'tags' => ['max' => 5, 'multiple' => true],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'thumbnail' => [],
            'title' => ['description' => true],
            'slug' => [],
            'brand.title' =>  ['alias' => 'belongs_to', 'label' => 'brand', 'relation' => 'brand'],
            'categories' => [],
            'tags' => ['tagType' => 'product'],
            'relation_count' => ['label' => 'specifications_count', 'relation' => 'specifications'],
            'is_active' => [],
            'published_at' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
            'seo' => [],
        ];
    }

    /** RELACTIONS */

    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), $this->getTaggableMorphName(), $this->getTaggableTableName())
            ->using($this->getPivotModelClassName())
            ->where('type', 'product')
            ->ordered();
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** SEO */

    public function getDynamicSEOData(): SEOData
    {
        // TODO
        $path = route('products.detail', ['product' => $this], false);

        return new SEOData(
            title: $this->seo->getTranslation('title', app()->currentLocale()) ?: $this->title,
            description: $this->seo->description ?: $this->description,
            author: $this->seo->author ?: config('app.name'),
            image: $this->seo->image ? Media::imgUrl($this->seo->image) : Media::imgUrl($this->img),
            enableTitleSuffix: false,
            alternates: $this->getAlternateTag($path),
            schema: SchemaCollection::make()->add(fn(SEOData $SEOData) => JsonLD::article($SEOData, $this)),
        );
    }

    /** OTHERS */

    protected static function newFactory()
    {
        return \Sharenjoy\NoahShop\Database\Factories\ProductFactory::new();
    }

    public function getReplicateAction($type)
    {
        $resourceName = 'products';
        $classname = $type == 'table' ? '\Filament\Tables\Actions\ReplicateAction' : '\Filament\Actions\ReplicateAction';
        return $classname::make()
            ->after(function (Model $replica, Model $ownerRecord): void {
                $replica->slug = $replica->slug . '-' . $replica->id;
                $replica->is_active = false;
                $replica->save();

                // 複製規格
                $specResults = $ownerRecord->specifications->pluck('spec_detail_name')->toArray();
                foreach ($specResults as $value) {
                    $replica->specifications()->create([
                        'spec_detail_name' => $value,
                        'is_active' => true,
                    ]);
                }
            })
            ->successRedirectUrl(function (Model $replica) use ($resourceName): string {
                $currentPanelId = Filament::getCurrentPanel()->getId();
                return route('filament.' . $currentPanelId . '.resources.' . $resourceName . '.edit', [
                    'record' => $replica,
                ]);
            });
    }
}
