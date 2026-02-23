<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordMailNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = config('app.frontend_url')
            . '/reset-password?token=' . $this->token
            . '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Recuperacao de senha - GeoContacts')
            ->view('emails.reset-password', [
                'resetUrl' => $resetUrl,
            ]);
    }
}
