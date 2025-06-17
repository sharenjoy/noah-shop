<?php

namespace Sharenjoy\NoahShop\Models\Survey;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sharenjoy\NoahShop\Casts\Survey\SeparatedByCommaAndSpace;
use Sharenjoy\NoahShop\Models\Survey\Entry;
use Sharenjoy\NoahShop\Models\Survey\Question;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Sharenjoy\NoahCms\Models\Traits\HasMediaLibrary;
use Spatie\Activitylog\Traits\LogsActivity;

class Answer extends Model
{
    use CommonModelTrait;
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use HasMediaLibrary;

    protected $table = 'srv_answers';

    protected $casts = [
        'value' => SeparatedByCommaAndSpace::class,
    ];

    public $translatable = [];

    protected array $sort = [
        'created_at' => 'desc',
    ];

    protected function formFields(): array
    {
        return [];
    }

    protected function tableFields(): array
    {
        return [
            'question.content' => ['label' => 'survey.title.question'],
            // 'entry.participant.name' => ['label' => 'survey.title.participant'],
            'participant' => TextColumn::make('participant')
                ->label(__('noah-shop::noah-shop.survey.title.participant'))
                ->state(function ($record) {
                    return $record->entry->participant->name ?? $record->entry->name;
                }),
            'value' => ['label' => 'survey.title.answer'],
            'created_at' => [],
            'updated_at' => [],
        ];
    }

    /**
     * The entry the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo(Entry::class);
    }

    /**
     * The question the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    protected static function newFactory()
    {
        return \Sharenjoy\NoahCms\Database\Factories\Survey\AnswerFactory::new();
    }
}
