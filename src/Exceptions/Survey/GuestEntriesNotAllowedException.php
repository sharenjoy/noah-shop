<?php

namespace Sharenjoy\NoahShop\Exceptions\Survey;

use Exception;

class GuestEntriesNotAllowedException extends Exception
{
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'Login is required for this survey.';
}
