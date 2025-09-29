<?php

namespace App\Vito\Plugins\Flowan\VitoSshLoginNotification\Notifications;

use App\Models\Server;
use App\Notifications\AbstractNotification;
use Illuminate\Notifications\Messages\MailMessage;

class ServerSshLogin extends AbstractNotification
{
    public function __construct(
        protected Server $server,
        protected string $user,
        protected string $ip,
        protected string $date,
    ) {}

    public function rawText(): string
    {
        return __("New SSH login has happened on server [:server].\n- User: :user\n- IP: :ip\n- Date: :date", [
            'server' => $this->server->name,
            'user' => $this->user,
            'ip' => $this->ip,
            'date' => $this->date,
        ]);
    }

    public function toEmail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('New SSH Login on :server', ['server' => $this->server->name]))
            ->line(__('New SSH login has happened on server [:server].', ['server' => $this->server->name]))
            ->line(__('User: :user', ['user' => $this->user]))
            ->line(__('IP: :ip', ['ip' => $this->ip]))
            ->line(__('Date: :date', ['date' => $this->date]));
    }
}
