<?php

namespace App\Domain\Property\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class PropertyStatus extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Available::class)
            ->allowTransition(Available::class, Rented::class)
            ->allowTransition(Rented::class, Available::class)
            ->allowTransition(Available::class, Maintenance::class)
            ->allowTransition(Maintenance::class, Available::class);
    }
}
