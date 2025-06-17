<?php

namespace Sharenjoy\NoahShop\Exceptions\Survey;

use Exception;

class MaxEntriesPerUserLimitExceeded extends Exception
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'Maximum entries per user exceeded.';
}
