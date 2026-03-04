<?php

namespace App\Domain\Negotiation\States;

class PendingVerification extends OfferStatus
{
    public static string $name = 'pending_verification';
}
