<?php

namespace App\Domain\Financial\States;

class Failed extends PaymentStatus
{
    /** @var string $name */
    public static $name = 'failed';
}
