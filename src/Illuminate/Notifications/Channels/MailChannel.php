<?php

namespace Illuminate\Notifications\Channels;

use Illuminate\Support\Str;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Notifications\Notification;

class MailChannel
{
    /**
     * The mailer implementation.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * Create a new mail channel instance.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send the given notification.
     *
     * @param  \Illuminate\Support\Collection  $notifiables
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiables, Notification $notification)
    {
        foreach ($notifiables as $notifiable) {
            if (! $notifiable->routeNotificationFor('mail')) {
                continue;
            }

            $message = $notification->toMail($notifiable);

            $this->mailer->send($message->view, $message->toArray(), function ($m) use ($notifiable, $notification, $message) {
                $m->to($notifiable->routeNotificationFor('mail'));

                $m->subject($message->subject ?: Str::title(
                    Str::snake(class_basename($notification), ' ')
                ));
            });
        }
    }
}