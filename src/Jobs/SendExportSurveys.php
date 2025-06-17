<?php

namespace Sharenjoy\NoahShop\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Sharenjoy\NoahShop\Models\Survey\Survey;
use Sharenjoy\NoahShop\Exports\SurveysExport;
use Sharenjoy\NoahShop\Mail\Export;

class SendExportSurveys implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user;

    public ?Collection $surveys;

    public ?Survey $survey;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $survey = null, $surveys = null)
    {
        $this->user = $user;
        $this->survey = $survey;
        $this->surveys = $surveys;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filename = now()->format('Y-m-d_his') . '-surveys.xlsx';

        if ($this->survey) {
            $filename = now()->format('Y-m-d_his') . '-' . urlencode($this->survey->title) . '.xlsx';
            $export = Excel::download(new SurveysExport(survey: $this->survey), $filename)->getFile();
        } elseif ($this->surveys) {
            $export = Excel::download(new SurveysExport(surveys: $this->surveys), $filename)->getFile();
        } else {
            $export = Excel::download(new SurveysExport(), $filename)->getFile();
        }

        $subject = __('Export ready:') . ' ' . $filename;

        Mail::to($this->user->email)->send(new Export($export, $filename, $subject));
    }
}
