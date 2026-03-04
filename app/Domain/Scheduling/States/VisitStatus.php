<?php

namespace App\Domain\Scheduling\States;

use App\Domain\Scheduling\Models\Visit;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends \Spatie\ModelStates\State<Visit>
 */
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
