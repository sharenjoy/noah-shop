@component('survey::questions.base', compact('question'))
    <input type="number" name="{{ $question->key }}" id="{{ $question->key }}" class="form-control"
           value="{{ $value ?? old($question->key) }}" {{ ($disabled ?? false) ? 'disabled' : '' }}>

    @slot('report')
        @if($includeResults ?? false)
            {{ number_format((new \Sharenjoy\NoahShop\Utils\SurveySummary($question))->average()) }} (Average)
        @endif
    @endslot
@endcomponent
