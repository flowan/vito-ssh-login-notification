<?php

namespace App\Vito\Plugins\Flowan\VitoSshLoginNotification\Actions;

use App\DTOs\DynamicField;
use App\DTOs\DynamicForm;
use App\Models\NotificationChannel;
use App\ServerFeatures\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Enable extends Action
{
    public function name(): string
    {
        return 'Enable';
    }

    public function active(): bool
    {
        return ! data_get($this->server->feature_data, 'ssh_login_notification', false);
    }

    public function form(): ?DynamicForm
    {
        return new DynamicForm([
            DynamicField::make('notification_channel')
                ->label('Notification Channel')
                ->select()
                ->options(
                    NotificationChannel::all(['id', 'label'])
                        ->map(fn ($channel) => $channel->label.' ('.$channel->id.')')
                        ->all()
                ),
        ]);
    }

    public function handle(Request $request): void
    {
        // Field options do not support key-value pairs, so we extract the ID from the selected option
        $notificationChannelId = Str::of($request->input('notification_channel'))->after('(')->before(')')->toString();
        $request->merge(['notification_channel' => $notificationChannelId]);

        $request->validate([
            'notification_channel' => 'required|exists:notification_channels,id',
        ]);

        $token = Str::random(32);

        $this->server->ssh()->exec(
            view('vito-ssh-login-notification::enable', [
                'url' => url("webhook/ssh-login-notification/{$this->server->id}"),
                'token' => $token,
            ]),
            'enable-ssh-login-notification',
        );

        $featureData = $this->server->feature_data ?? [];
        $featureData['ssh_login_notification'] = true;
        $featureData['ssh_login_notification_channel'] = (int) $request->input('notification_channel');
        $featureData['ssh_login_notification_token'] = $token;
        $this->server->feature_data = $featureData;
        $this->server->save();

        $request->session()->flash('success', 'SSH login notification has been enabled for this server.');
    }
}
