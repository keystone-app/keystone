<?php

namespace App\Domain\Scheduling\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class VisitStatus extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransition(Pending::class, Scheduled::class)
            ->allowTransition(Pending::class, Rejected::class);
    }
}
