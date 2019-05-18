<?php
/*
 * This file is part of AwesomeProject.
 *
 * Copyright (c) 2017 Opensoft (http://opensoftdev.com)
 *
 * The unauthorized use of this code outside the boundaries of
 * Opensoft is prohibited.
 */

namespace Fightmaster\Trailrun\Competition\Handler\Exception;


use Throwable;

class CompetitionHasResultsData extends \DomainException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Competition has results';
        parent::__construct($message, $code, $previous);
    }
}
