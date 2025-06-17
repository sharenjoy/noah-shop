<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    @php
        $answers = $getRecord()->answers;
        $survey = $getRecord()->survey;
        $participant = $getRecord()->participant;
    @endphp

    <div class="w-full max-w-full overflow-x-auto">
        <ul class="divide-y divide-gray-100">
            @foreach ($answers as $answer)
                <li class="flex py-6">
                    <div class="left-col">
                        <h3 class="text-lg text-gray-500">{!! nl2br($answer->question->content) !!}</h3>
                    </div>
                    <div class="flex-1">
                        @if ($answer->question->type === 'text')
                            <h3 class="text-md font-medium text-gray-700">{!! nl2br($answer->value) !!}</h3>
                        @elseif ($answer->question->type === 'number')
                            <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                        @elseif ($answer->question->type === 'radio')
                            <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                        @elseif ($answer->question->type === 'multiselect')
                            <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                        @elseif ($answer->question->type === 'file')
                            <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                        @elseif ($answer->question->type === 'price')
                            <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                        @else
                            <span class="text-gray-500">未定義的問題類型</span>
                        @endif
                    </div>
                </li>
            @endforeach

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">填寫人</h3>
                </div>
                <div class="flex-1">
                    <h3 class="text-md font-medium text-gray-700">{{ $participant?->name ?? $getRecord()->name }}</h3>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">狀態</h3>
                </div>
                <div class="flex-1">
                    <h3 class="text-md font-medium text-gray-700">{{ \Sharenjoy\NoahShop\Enums\SurveyEntryStatus::getLabelFromOption($getRecord()->status) }}</h3>
                    <h4 class="text-xs font-medium text-gray-700 mt-2">{!! nl2br($getRecord()->status()->reason ?? '-') !!}</h4>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">日期</h3>
                </div>
                <div class="flex-1">
                    <div class="text-sm">
                        <div class="pb-2">建立於 {{ $getRecord()->created_at->diffForHumans() }}<br>{{ $getRecord()->created_at }}</div>
                        <div>上次更新 {{ $getRecord()->updated_at->diffForHumans() }}<br>{{ $getRecord()->updated_at }}</div>
                    </div>
                </div>
            </li>

        </ul>
    </div>

</x-dynamic-component>

<style>
    .left-col {
        width: 300px;
    }
    @media (max-width: 600px) {
        .left-col {
            width: 100px;
        }
    }
</style>
