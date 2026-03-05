<?php

namespace App\Domain\Financial\States;

use App\Domain\Financial\Models\Payment;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

/**
 * @extends \Spatie\ModelStates\State<Payment>
 */
abstract class PaymentStatus extends State
{
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransition(Pending::class, Completed::class)
            ->allowTransition(Pending::class, Failed::class);
    }
}
