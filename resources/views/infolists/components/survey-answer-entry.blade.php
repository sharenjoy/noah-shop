<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    @php
        $answer = $getRecord();
        $question = $getRecord()->question;
        $entryRecord = $getRecord()->entry;
    @endphp

    <div class="w-full max-w-full overflow-x-auto">
        <ul class="divide-y divide-gray-100">
            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">{!! nl2br($question->content) !!}</h3>
                </div>
                <div class="flex-1">
                    @if ($question->type === 'text')
                        <h3 class="text-md font-medium text-gray-700">{!! nl2br($answer->value) !!}</h3>
                    @elseif ($question->type === 'number')
                        <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                    @elseif ($question->type === 'radio')
                        <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                    @elseif ($question->type === 'multiselect')
                        <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                    @elseif ($question->type === 'file')
                        <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                    @elseif ($question->type === 'price')
                        <h3 class="text-md font-medium text-gray-700">{{ $answer->value }}</h3>
                    @else
                        <span class="text-gray-500">未定義的問題類型</span>
                    @endif
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">問卷</h3>
                </div>
                <div class="flex-1">
                    <h3 class="text-md font-medium text-gray-700">{!! nl2br($entryRecord->survey->title) !!}</h3>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">填寫人</h3>
                </div>
                <div class="flex-1">
                    <h3 class="text-md font-medium text-gray-700">{{ $entryRecord->participant->name ?? $entryRecord->name }}</h3>
                </div>
            </li>

            <li class="flex py-6">
                <div class="left-col">
                    <h3 class="text-lg text-gray-500">日期</h3>
                </div>
                <div class="flex-1">
                    <div class="text-sm">
                        <div class="pb-2">建立於 {{ $answer->created_at->diffForHumans() }}<br>{{ $answer->created_at }}</div>
                        <div>上次更新 {{ $answer->updated_at->diffForHumans() }}<br>{{ $answer->updated_at }}</div>
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
