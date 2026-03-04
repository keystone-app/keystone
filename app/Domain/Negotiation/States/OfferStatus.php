<?php

namespace App\Domain\Negotiation\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class OfferStatus extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransition(Pending::class, Accepted::class)
            ->allowTransition(Pending::class, Rejected::class)
            ->allowTransition(Pending::class, Countered::class)
            ->allowTransition(Countered::class, Accepted::class)
            ->allowTransition(Countered::class, Rejected::class)
            ->allowTransition(Accepted::class, AwaitingDocuments::class)
            ->allowTransition(AwaitingDocuments::class, PendingVerification::class)
            ->allowTransition(PendingVerification::class, Verified::class);
    }
}
