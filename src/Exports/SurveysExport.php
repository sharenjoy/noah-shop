<?php

namespace Sharenjoy\NoahShop\Exports;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Sharenjoy\NoahShop\Models\Survey\Survey;

class SurveysExport implements FromCollection, WithHeadings, WithMapping
{
    public ?Collection $surveys;

    public ?Survey $survey;

    public function __construct(?Collection $surveys = null, ?Survey $survey = null)
    {
        $this->surveys = $surveys;
        $this->survey = $survey;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $rawExpression = "
            SELECT
                users.name as user_name,
                users.email as user_email,
                JSON_EXTRACT(srv_surveys.title, '$.zh_TW') as survey_name,
                JSON_EXTRACT(srv_questions.content, '$.zh_TW') as question_content,
                srv_answers.value as answer_value,
                srv_entries.created_at as entry_created_at
            FROM
                srv_answers
                JOIN srv_questions ON srv_questions.id = srv_answers.question_id
                JOIN srv_entries ON srv_entries.id = srv_answers.entry_id
                JOIN srv_surveys ON srv_surveys.id = srv_entries.survey_id
                JOIN users ON users.id = srv_entries.participant_id
        ";

        if ($this->survey) {
            $rawExpression = $rawExpression . ' where srv_surveys.id = ' . $this->survey->id;
        }

        if ($this->surveys) {
            $surveyIdsString = implode(', ', $this->surveys->pluck('id')->toArray());
            $rawExpression = $rawExpression . ' where srv_surveys.id In (' . $surveyIdsString . ')';
        }

        $expression = DB::raw($rawExpression)
            ->getValue(DB::connection()->getQueryGrammar());

        return collect(DB::select($expression));
    }

    public function map($survey): array
    {
        return [
            $survey->user_name,
            $survey->user_email,
            $survey->survey_name,
            $survey->question_content,
            $survey->answer_value,
            $survey->entry_created_at,
        ];
    }

    public function headings(): array
    {
        return [
            'user_name',
            'user_email',
            'survey_name',
            'question_content',
            'answer_value',
            'entry_created_at',
        ];
    }
}
