<?php

namespace App\Domain\Legal\States;

class WaitingTenantSignature extends LeaseStatus
{
    public static $name = 'waiting_tenant_signature';
}
