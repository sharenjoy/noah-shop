<?php

namespace Sharenjoy\NoahShop\Commands;

use Illuminate\Console\Command;

class NoahShopCommand extends Command
{
    public $signature = 'noah-shop';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
