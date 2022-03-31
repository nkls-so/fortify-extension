<?php

declare(strict_types=1);

namespace Nkls\FortifyExtension\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Nkls\FortifyExtension\Enums\TwoFactorChannel;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class TOTPCode extends Notification
{
    use Queueable;

    protected $totp;

    /**
     * Create a new notification instance.
     *
     * @param mixed $totp
     *
     * @return void
     */
    public function __construct($totp)
    {
        $this->totp = $totp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        if (TwoFactorChannel::TOTP_EMAIL === $notifiable->two_factor_channel) {
            return ['mail'];
        }
        if (TwoFactorChannel::TOTP_SMS === $notifiable->two_factor_channel) {
            return [TwilioChannel::class];
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject(Lang::get('fortify-extension::fortify-extension.totp_email_subject'))
            ->line(Lang::get('fortify-extension::fortify-extension.totp_email_first_line'))
            ->line($this->totp)
            ->line(Lang::get('fortify-extension::fortify-extension.totp_email_last_line'))
        ;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content(Lang::get('fortify-extension::fortify-extension.totp_sms_message', ['totp' => $this->totp]));
    }
}
