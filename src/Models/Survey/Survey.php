<?php

namespace Sharenjoy\NoahShop\Models\Survey;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Sharenjoy\NoahShop\Models\Survey\Entry;
use Sharenjoy\NoahShop\Models\Survey\Question;
use Sharenjoy\NoahShop\Models\Survey\Section;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Sharenjoy\NoahCms\Models\Traits\HasMenus;
use Sharenjoy\NoahCms\Utils\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Survey extends Model
{
    use CommonModelTrait;
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use HasTranslations;
    use HasMediaLibrary;
    use HasMenus;
    use HasSEO;

    protected $table = 'srv_surveys';

    protected $casts = [
        'album' => 'array',
        'limit' => 'boolean',
        'purchaseable' => 'boolean',
        'purchase_depends_on_option' => 'boolean',
        'is_active' => 'boolean',
        'forever' => 'boolean',
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
        'display_expired_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public $translatable = [
        'title',
        'description',
        'content',
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
                ],
                'slug' => ['maxLength' => 50, 'required' => true],
                'description' => ['rules' => ['string']],
                'content' => ['profile' => 'simple'],
            ],
            'right' => [
                'img' => [],
                'album' => [],
                'is_active' => ['required' => true],
                'published_at' => FormSection::make()->schema([
                    DateTimePicker::make('published_at')
                        ->displayFormat('Y-m-d H:i:s') // 顯示格式
                        ->label(__('noah-shop::noah-shop.published_at'))
                        ->placeholder('2020-03-18 09:48:00')
                        ->prefixIcon('heroicon-o-clock')
                        ->required()
                        ->rules(['date'])
                        ->format('Y-m-d H:i:s')
                        ->live()
                        ->native(false),
                ]),
                'display_expired_at' => FormSection::make()->schema([
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
                ->label(__('noah-shop::noah-shop.survey.title.duration_set'))
                ->toggleable(),
            'seo' => [],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /** RELACTIONS */

    /**
     * The survey sections.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * The survey questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * The survey entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    /**
     * Check if survey accepts guest entries.
     *
     * @return bool
     */
    public function acceptsGuestEntries()
    {
        return $this->settings['accept-guest-entries'] ?? false;
    }

    /**
     * The maximum number of entries a participant may submit.
     *
     * @return int|null
     */
    public function limitPerParticipant()
    {
        if ($this->acceptsGuestEntries()) {
            return;
        }

        $limit = $this->settings['limit-per-participant'] ?? 1;

        return $limit !== -1 ? $limit : null;
    }

    /**
     * Survey entries by a participant.
     *
     * @param  Model  $participant
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entriesFrom(Model $participant)
    {
        return $this->entries()->where('participant_id', $participant->id);
    }

    /**
     * Last survey entry by a participant.
     *
     * @param  Model  $participant
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lastEntry(?Model $participant = null)
    {
        return $participant === null ? null : $this->entriesFrom($participant)->first();
    }

    /**
     * Check if a participant is eligible to submit the survey.
     *
     * @param  Model|null  $model
     * @return bool
     */
    public function isEligible(?Model $participant = null)
    {
        if ($participant === null) {
            return $this->acceptsGuestEntries();
        }

        if ($this->limitPerParticipant() === null) {
            return true;
        }

        return $this->limitPerParticipant() > $this->entriesFrom($participant)->count();
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
     * Combined validation rules of the survey.
     *
     * @return mixed
     */
    public function getRulesAttribute()
    {
        return $this->questions->mapWithKeys(function ($question) {
            return [$question->key => $question->rules];
        })->all();
    }

    /** SEO */

    public function getDynamicSEOData(): SEOData
    {
        // TODO
        $path = route('surveys.detail', ['survey' => $this], false);

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

    protected static function newFactory()
    {
        return \Sharenjoy\NoahShop\Database\Factories\Survey\SurveyFactory::new();
    }
}
