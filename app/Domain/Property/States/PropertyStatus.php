<?php

namespace App\Domain\Property\States;

use App\Domain\Property\Models\Property;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends \Spatie\ModelStates\State<Property>
 */
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
