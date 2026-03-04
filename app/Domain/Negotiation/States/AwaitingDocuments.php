<?php

namespace App\Domain\Negotiation\States;

class AwaitingDocuments extends OfferStatus
{
    public static string $name = 'awaiting_documents';
}
