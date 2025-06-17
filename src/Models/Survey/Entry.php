<?php

namespace Sharenjoy\NoahShop\Models\Survey;

use Coolsam\NestedComments\Concerns\HasComments;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use Sharenjoy\NoahShop\Enums\SurveyEntryStatus;
use Sharenjoy\NoahShop\Exceptions\Survey\GuestEntriesNotAllowedException;
use Sharenjoy\NoahShop\Exceptions\Survey\MaxEntriesPerUserLimitExceeded;
use Sharenjoy\NoahShop\Models\Survey\Answer;
use Sharenjoy\NoahShop\Models\Survey\Question;
use Sharenjoy\NoahShop\Models\Survey\Survey;
use Sharenjoy\NoahCms\Models\Traits\CommonModelTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\ModelStatus\HasStatuses;

class Entry extends Model
{
    use CommonModelTrait;
    use SoftDeletes;
    use LogsActivity;
    use HasStatuses;
    use HasComments;

    protected $table = 'srv_entries';

    protected $casts = [];

    protected $appends = ['status'];

    public $translatable = [];

    protected array $sort = [
        'created_at' => 'desc',
    ];

    /**
     * Boot the entry.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        //Prevent submission of entries that don't meet the parent survey's constraints.
        static::creating(function (self $entry) {
            $entry->validateParticipant();
            $entry->validateMaxEntryPerUserRequirement();
        });

        //Automatically set the status to 'Established' when created a new entry.
        static::created(function (self $entry) {
            if (! $entry->status) {
                $entry->setStatus(SurveyEntryStatus::Established->value);
            }
        });
    }

    public function isValidStatus(string $name, ?string $reason = null): bool
    {
        if (! array_key_exists($name, SurveyEntryStatus::options())) {
            return false;
        }

        return true;
    }

    protected function formFields(): array
    {
        return [];
    }

    protected function tableFields(): array
    {
        $entryStatuses = SurveyEntryStatus::options();

        return [
            'survey.title' => ['alias' => 'belongs_to', 'label' => 'survey.title.survey', 'relation' => 'survey', 'relation_route' => 'survey.surveys', 'relation_column' => 'survey', 'operation' => 'view'],
            'participant' => TextColumn::make('participant')
                ->label(__('noah-shop::noah-shop.survey.title.participant'))
                ->state(function ($record) {
                    return $record->participant->name ?? $record->name;
                }),
            'status' => TextColumn::make('status')->label(__('noah-cms::noah-cms.status'))
                ->state(function ($record) use ($entryStatuses) {
                    return $entryStatuses[$record->status] ?? '-';
                })->badge()->sortable()->toggleable(),
            'created_at' => [],
            'updated_at' => [],
        ];
    }

    /**
     * The answers within the entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * The survey the entry belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    /**
     * The participant that the entry belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function participant()
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function getStatusAttribute(): ?string
    {
        if ($this->status === null) {
            return null;
        }

        return $this->status();
    }

    /**
     * Set the survey the entry belongs to.
     *
     * @param  Survey  $survey
     * @return $this
     */
    public function for(Survey $survey)
    {
        $this->survey()->associate($survey);

        return $this;
    }

    /**
     * Set the participant who the entry belongs to.
     *
     * @param  Model|null  $model
     * @return $this
     */
    public function by(?Model $model = null)
    {
        $this->participant()->associate($model);

        return $this;
    }

    /**
     * Create an entry from an array.
     *
     * @param  array  $values
     * @return $this
     */
    public function fromArray(array $values)
    {
        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }

            $answer_class = new Answer;

            $this->answers->add($answer_class::make([
                'question_id' => substr($key, 1),
                'entry_id' => $this->id,
                'value' => $value,
            ]));
        }

        return $this;
    }

    /**
     * The answer for a given question.
     *
     * @param  Question  $question
     * @return mixed|null
     */
    public function answerFor(Question $question)
    {
        $answer = $this->answers()->where('question_id', $question->id)->first();

        return isset($answer) ? $answer->value : null;
    }

    /**
     * Save the model and all of its relationships.
     * Ensure the answers are automatically linked to the entry.
     *
     * @return bool
     */
    public function push()
    {
        $this->save();

        foreach ($this->answers as $answer) {
            $answer->entry_id = $this->id;
        }

        return parent::push();
    }

    /**
     * Validate participant's legibility.
     *
     * @throws GuestEntriesNotAllowedException
     */
    public function validateParticipant()
    {
        if ($this->survey->acceptsGuestEntries()) {
            return;
        }

        if ($this->participant_id !== null) {
            return;
        }

        throw new GuestEntriesNotAllowedException();
    }

    /**
     * Validate if entry exceeds the survey's
     * max entry per participant limit.
     *
     * @throws MaxEntriesPerUserLimitExceeded
     */
    public function validateMaxEntryPerUserRequirement()
    {
        $limit = $this->survey->limitPerParticipant();

        if ($limit === null) {
            return;
        }

        $count = static::where('participant_id', $this->participant_id)
            ->where('survey_id', $this->survey->id)
            ->count();

        if ($count >= $limit) {
            throw new MaxEntriesPerUserLimitExceeded();
        }
    }
}
