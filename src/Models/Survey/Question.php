<?php

namespace Sharenjoy\NoahShop\Models\Survey;

use Filament\Forms\Components\Section as FormSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharenjoy\NoahShop\Models\Survey\Answer;
use Sharenjoy\NoahShop\Models\Survey\Section;
use Sharenjoy\NoahShop\Models\Survey\Survey;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\Translatable\HasTranslations;

class Question extends Model implements Sortable
{
    use CommonModelTrait;
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use SortableTrait;
    use HasTranslations;
    use HasMediaLibrary;

    protected $table = 'srv_questions';

    protected $casts = [
        'rules' => 'array',
        'options' => 'array',
    ];

    public $translatable = [
        'content',
        'help',
        'options',
    ];

    protected array $sort = [
        'order_column' => 'asc',
    ];

    /**
     * Boot the question.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        //Ensure the question's survey is the same as the section it belongs to.
        static::creating(function (self $question) {
            $question->load('section');

            if ($question->section) {
                $question->survey_id = $question->section->survey_id;
            }
        });
    }

    protected function formFields(): array
    {
        return [
            'left' => [
                'content' => [
                    'alias' => 'description',
                    'label' => 'survey.title.question',
                    'required' => true,
                    'rules' => ['required', 'string'],
                ],
                'help' => [
                    'alias' => 'description',
                    'label' => 'survey.title.question_help',
                    'rules' => ['string'],
                ],
                'type' => FormSection::make()->schema([
                    Select::make('type')
                        ->label(__('noah-shop::noah-shop.survey.title.type'))
                        ->required()
                        ->reactive()
                        ->options(
                            collect(config('noah-shop.survey.question.types'))
                                ->mapWithKeys(fn($label, $key) => [
                                    $key => __('noah-shop::noah-shop.survey.title.' . strtolower($label))
                                ])
                                ->toArray()
                        ),
                ]),
                'options' => FormSection::make()->schema([
                    TagsInput::make('options')
                        ->label(__('noah-shop::noah-shop.survey.title.options'))
                        ->placeholder('New option')
                        ->helperText(__('noah-shop::noah-shop.survey.help.options'))
                        ->required()
                        ->visible(fn(Get $get) => $get('type') == 'radio' || $get('type') == 'multiselect'),
                ])->visible(fn(Get $get) => $get('type') == 'radio' || $get('type') == 'multiselect'),
                'price' => FormSection::make()->schema([
                    TextInput::make('price')
                        ->label(__('noah-shop::noah-shop.survey.title.price'))
                        ->placeholder('300')
                        ->helperText(__('noah-shop::noah-shop.survey.help.price'))
                        ->required()
                        ->visible(fn(Get $get) => $get('type') == 'price'),
                ])->visible(fn(Get $get) => $get('type') == 'price'),
                'rules' => FormSection::make()->schema([
                    TagsInput::make('rules')
                        ->label(__('noah-shop::noah-shop.survey.title.rules'))
                        ->placeholder('New rule')
                        ->helperText(__('noah-shop::noah-shop.survey.help.rules')),
                ]),
            ],
            'right' => [
                // 'album' => [],
                // 'is_active' => ['required' => true],
            ],
        ];
    }

    protected function tableFields(): array
    {
        return [
            'content' => ['label' => 'survey.title.question'],
            'type' => TextColumn::make('type')
                ->label(__('noah-shop::noah-shop.survey.title.type'))
                ->sortable()
                ->state(function ($record) {
                    return __('noah-shop::noah-shop.survey.title.' . $record->type);
                })
                ->toggleable(isToggledHiddenByDefault: $this->content['isToggledHiddenByDefault'] ?? false),
            'section.title' => ['label' => 'survey.title.section'],
            'created_at' => ['isToggledHiddenByDefault' => true],
            'updated_at' => ['isToggledHiddenByDefault' => true],
        ];
    }

    /**
     * The survey the question belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * The section the question belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * The answers that belong to the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * The question's validation rules.
     *
     * @param  $value
     * @return array|mixed
     */
    public function getRulesAttribute($value)
    {
        $value = $this->castAttribute('rules', $value);

        return $value !== null ? $value : [];
    }

    /**
     * The unique key representing the question.
     *
     * @return string
     */
    public function getKeyAttribute()
    {
        return "q{$this->id}";
    }

    /**
     * Scope a query to only include questions that
     * don't belong to any sections.
     *
     * @param  $query
     * @return mixed
     */
    public function scopeWithoutSection($query)
    {
        return $query->where('section_id', null);
    }

    protected static function newFactory()
    {
        return \Sharenjoy\NoahShop\Database\Factories\Survey\QuestionFactory::new();
    }
}
