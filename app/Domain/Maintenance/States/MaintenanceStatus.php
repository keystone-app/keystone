<?php

namespace App\Domain\Maintenance\States;

use App\Domain\Maintenance\Models\MaintenanceRequest;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends \Spatie\ModelStates\State<MaintenanceRequest>
 */
abstract class MaintenanceStatus extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Reported::class)
            ->allowTransition(Reported::class, InProgress::class)
            ->allowTransition(InProgress::class, Resolved::class);
    }
}
