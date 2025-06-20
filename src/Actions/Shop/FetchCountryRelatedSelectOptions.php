<?php

namespace Sharenjoy\NoahShop\Actions\Shop;

use Lorisleiva\Actions\Concerns\AsAction;
use Webpatser\Countries\Countries;

class FetchCountryRelatedSelectOptions
{
    use AsAction;

    public function handle(string $type): array
    {
        $options = [];

        if ($type == 'calling_code') {
            Countries::all()->map(function ($item) use (&$options) {
                return $options[$item->calling_code] = '(' . $item->calling_code . ')' . $item->name;
            });
        } elseif ($type == 'country') {
            Countries::all()->map(function ($item) use (&$options) {
                return $options[$item->name] = $item->name;
            });
        }

        return $options;
    }
}
