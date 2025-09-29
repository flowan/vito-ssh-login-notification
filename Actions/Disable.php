<?php

namespace App\Vito\Plugins\Flowan\VitoSshLoginNotification\Actions;

use App\ServerFeatures\Action;
use Illuminate\Http\Request;

class Disable extends Action
{
    public function name(): string
    {
        return 'Disable';
    }

    public function active(): bool
    {
        return data_get($this->server->feature_data, 'ssh_login_notification', false);
    }

    public function handle(Request $request): void
    {
        $this->server->ssh()->exec(
            view('vito-ssh-login-notification::disable'),
            'disable-ssh-login-notification',
        );

        $featureData = $this->server->feature_data ?? [];
        unset($featureData['ssh_login_notification']);
        unset($featureData['ssh_login_notification_channel']);
        unset($featureData['ssh_login_notification_token']);
        $this->server->feature_data = $featureData;
        $this->server->save();

        $request->session()->flash('success', 'SSH login notification has been disabled for this server.');
    }
}
