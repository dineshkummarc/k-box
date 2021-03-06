<?php

namespace KBox\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use KBox\UserQuota;

class QuotaLimitNotification extends Notification
{
    use Queueable;

    public $quota;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UserQuota $userQuota)
    {
        $this->quota = $userQuota;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('quota.notifications.limit.subject'))
            ->line(trans('quota.notifications.limit.text', ['threshold' => $this->quota->threshold]))
            ->action(trans('quota.notifications.action'), route('profile.storage.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
