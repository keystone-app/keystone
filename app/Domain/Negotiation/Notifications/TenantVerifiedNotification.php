<?php

namespace App\Domain\Negotiation\Notifications;

use App\Domain\Negotiation\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Offer $offer)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tenant Verified for ' . $this->offer->property->name)
            ->line('The tenant ' . $this->offer->user->name . ' has successfully completed income verification.')
            ->line('A lease draft has been automatically created.')
            ->action('View Offer', url('/offers/' . $this->offer->id))
            ->line('Thank you for using Keystone!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'offer_id' => $this->offer->id,
            'property_id' => $this->offer->property_id,
            'tenant_name' => $this->offer->user->name,
            'message' => 'Tenant ' . $this->offer->user->name . ' has been verified.',
        ];
    }
}
