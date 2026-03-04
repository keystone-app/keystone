<?php

namespace App\Domain\Legal\States;

use App\Domain\Legal\Models\Lease;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends \Spatie\ModelStates\State<Lease>
 */
abstract class LeaseStatus extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Draft::class)
            ->allowTransition(Draft::class, WaitingLandlordSignature::class)
            ->allowTransition(WaitingLandlordSignature::class, WaitingTenantSignature::class)
            ->allowTransition(WaitingTenantSignature::class, Active::class);
    }
}
