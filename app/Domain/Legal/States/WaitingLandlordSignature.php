<?php

namespace App\Domain\Legal\States;

class WaitingLandlordSignature extends LeaseStatus
{
    public static $name = 'waiting_landlord_signature';
}
