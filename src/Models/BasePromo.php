<?php

namespace Sharenjoy\NoahShop\Models;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Sharenjoy\NoahShop\Enums\ObjectiveType;
use Sharenjoy\NoahShop\Enums\PromoAutoGenerateType;
use Sharenjoy\NoahShop\Enums\PromoDiscountType;
use Sharenjoy\NoahShop\Enums\PromoType;
use Sharenjoy\NoahShop\Models\Giftproduct;
use Sharenjoy\NoahShop\Models\Objective;
use Sharenjoy\NoahShop\Models\OrderItem;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Sharenjoy\NoahCms\Models\Traits\HasMenus;
use Sharenjoy\NoahCms\Models\Traits\HasTags;
use Sharenjoy\NoahShop\Models\UserCoupon;
use Sharenjoy\NoahShop\Tables\Columns\PromoAutoGenerateEventColumn;
use Sharenjoy\NoahShop\Tables\Columns\PromoTypeColumn;
use Sharenjoy\NoahCms\Utils\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class BasePromo extends Model
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use HasTranslations;
    use HasMediaLibrary;
    use HasMenus;
    use HasTags;
    use HasSEO;

    protected $casts = [
        'type' => PromoType::class,
        'discount_type' => PromoDiscountType::class,
        'auto_generate_type' => PromoAutoGenerateType::class,
        'album' => 'array',
        'forever' => 'boolean',
        'combined' => 'boolean',
        'entire_order_discount_percent' => 'boolean',
        'auto_assign_to_user' => 'boolean',
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
        'display_expired_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'online',
        'show_up',
        'generatable',
    ];

    public $translatable = [
        'title',
        'description',
        'content',
    ];

    protected array $sort = [
        'published_at' => 'desc',
        'id' => 'desc',
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
                'description' => ['rules' => ['string']],
                'content' => ['profile' => 'simple'],
            ],
            'right' => [
                'img' => [],
                'album' => [],
                'is_active' => ['required' => true],
                'published_at' => Section::make()->schema([
                    DateTimePicker::make('published_at')
                        ->displayFormat('Y-m-d H:i:s') // 顯示格式
                        ->label(__('noah-cms::noah-cms.published_at'))
                        ->placeholder('2020-03-18 09:48:00')
                        ->prefixIcon('heroicon-o-clock')
                        ->format('Y-m-d H:i:s')
                        ->required()
                        ->rules(['date'])
                        ->live()
                        ->native(false),
                ]),
                'display_expired_at' => Section::make()->schema([
                    DateTimePicker::make('display_expired_at')
                        ->displayFormat('Y-m-d H:i:s') // 顯示格式
                        ->label(__('noah-shop::noah-shop.shop.promo.title.display_expired_at'))
                        ->helperText(new HtmlString(__('noah-shop::noah-shop.shop.promo.help.display_expired_at')))
                        ->placeholder('2020-03-18 09:48:00')
                        ->prefixIcon('heroicon-o-clock')
                        ->format('Y-m-d H:i:s')
                        ->rules(['date', 'after_or_equal:published_at'])
                        ->minDate(fn(Get $get) => $get('published_at'))
                        ->native(false),
                ]),
                'menus' => [],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'thumbnail' => [],
            'title' => ['description' => true],
            'slug' => [],
            'promo' => PromoTypeColumn::make('type')
                ->label(__('noah-cms::noah-cms.promo'))
                ->toggleable(),
            'promo_event' => PromoAutoGenerateEventColumn::make('auto_generate_type')
                ->label(__('noah-shop::noah-shop.shop.promo.title.combined') . '/' . __('noah-shop::noah-shop.shop.promo.title.auto_generate_type'))
                ->toggleable(),
            'online' => ['type' => 'boolean', 'label' => 'online'],
            'is_active' => [],
            'show_up' => ['type' => 'boolean', 'label' => 'show_up'],
            'published_at' => [],
            'duration' => TextColumn::make('forever')
                ->html()
                ->size(TextColumnSize::Small)
                ->sortable()
                ->formatStateUsing(function ($record) {
                    if ($record->forever) {
                        return '<div class="pb-2">永久有效</div>';
                    }
                    return '<div><div class="pb-2">開始於 ' . $record->started_at->diffForHumans() . '<br>' . $record->started_at . '</div><div>到期於 ' . $record->expired_at->diffForHumans() . '<br>' . $record->expired_at . '</div></div>';
                })
                ->label(__('noah-shop::noah-shop.shop.promo.title.duration'))
                ->toggleable(),
            'seo' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    public function promoTags(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), $this->getTaggableMorphName(), $this->getTaggableTableName())
            ->using($this->getPivotModelClassName())
            ->where('type', 'promo')
            ->ordered();
    }

    public function orderItems(): MorphToMany
    {
        return $this->morphedByMany(OrderItem::class, 'promoable', foreignPivotKey: 'promo_id', relatedPivotKey: 'promoable_id');
    }

    public function giftproducts(): MorphToMany
    {
        return $this->morphedByMany(Giftproduct::class, 'promoable', foreignPivotKey: 'promo_id', relatedPivotKey: 'promoable_id');
    }

    public function objectives(): MorphToMany
    {
        return $this->morphedByMany(Objective::class, 'promoable', foreignPivotKey: 'promo_id', relatedPivotKey: 'promoable_id');
    }

    public function userObjectives(): MorphToMany
    {
        return $this->morphedByMany(Objective::class, 'promoable', foreignPivotKey: 'promo_id', relatedPivotKey: 'promoable_id')
            ->whereType(ObjectiveType::User->value);
    }

    public function productObjectives(): MorphToMany
    {
        return $this->morphedByMany(Objective::class, 'promoable', foreignPivotKey: 'promo_id', relatedPivotKey: 'promoable_id')
            ->whereType(ObjectiveType::Product->value);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(UserCoupon::class);
    }

    /** SCOPES */

    /** EVENTS */

    /** SEO */

    public function getDynamicSEOData(): SEOData
    {
        // TODO
        $path = route('promos.detail', ['promo' => $this], false);

        return new SEOData(
            title: $this->seo->getTranslation('title', app()->currentLocale()) ?: $this->title,
            description: $this->seo->description ?: $this->description,
            author: $this->seo->author ?: config('app.name'),
            image: $this->seo->image ? Media::imgUrl($this->seo->image) : Media::imgUrl($this->img),
            enableTitleSuffix: false,
            alternates: $this->getAlternateTag($path),
            // schema: SchemaCollection::make()->add(fn(SEOData $SEOData) => JsonLD::article($SEOData, $this)),
        );
    }

    /** OTHERS */

    protected static function newFactory()
    {
        return \Sharenjoy\NoahShop\Database\Factories\PromoFactory::new();
    }

    /**
     * Spatie Activity Log 會自動呼叫 model 的 getMorphClass() 來決定 subject_type
     * 你如果覆寫這個 method，就能指定寫進 log 的是什麼類別
     * 所以不管外面操作的是 NewOrder、IssuedOrder，
     * log 記錄時都統一成 \Sharenjoy\NoahShop\Models\Order
     * @return string
     */
    public function getMorphClass()
    {
        return \Sharenjoy\NoahShop\Models\Promo::class; // 你想要寫入 activity_log 裡的 class 名稱
    }

    public function online(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->is_active) {
                    return false;
                }

                $now = now();

                // published_at 必須小於等於現在時間
                if ($this->published_at !== null && $this->published_at->gt($now)) {
                    return false;
                }

                if ($this->forever ?? false) {
                    return true;
                }

                return ($this->started_at === null || $this->started_at->lte($now)) &&
                    ($this->expired_at === null || $this->expired_at->gte($now));
            },
        );
    }

    public function showUp(): Attribute
    {
        return Attribute::make(
            get: function () {
                // published_at 必須小於等於現在時間
                if ($this->display_expired_at === null) {
                    return false;
                }

                return $this->display_expired_at->gt(now());
            },
        );
    }

    /**
     * 這個優惠券是否可以產生
     * @return Attribute
     */
    public function generatable(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->is_active) {
                    return false;
                }

                if (! $this->auto_assign_to_user) {
                    return false;
                }

                if ($this->forever ?? false) {
                    return true;
                }

                $now = now();

                return ($this->started_at === null || $this->started_at->lte($now)) &&
                    ($this->expired_at === null || $this->expired_at->gte($now));
            },
        );
    }
}
