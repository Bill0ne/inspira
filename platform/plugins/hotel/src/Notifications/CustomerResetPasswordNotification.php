<?php

namespace Botble\Hotel\Notifications;

use Botble\Base\Facades\EmailHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CustomerResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Reset-Link direkt aus Token + Kunden-E-Mail bauen (nicht aus request(),
        // damit es auch im Queue-Worker ohne HTTP-Kontext korrekt funktioniert).
        $resetLink = route('customer.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $emailHandler = EmailHandler::setModule(HOTEL_MODULE_SCREEN_NAME)
            ->setType('plugins')
            ->setTemplate('customer-password-reset')
            ->addTemplateSettings(HOTEL_MODULE_SCREEN_NAME, config('plugins.hotel.email', []))
            ->setVariableValue('reset_link', $resetLink);

        return (new MailMessage())
            ->view(['html' => new HtmlString($emailHandler->getContent())])
            ->subject($emailHandler->getSubject());
    }
}
