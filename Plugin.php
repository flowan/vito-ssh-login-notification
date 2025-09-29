<?php

namespace App\Vito\Plugins\Flowan\VitoSshLoginNotification;

use App\Plugins\AbstractPlugin;
use App\Plugins\RegisterServerFeature;
use App\Plugins\RegisterServerFeatureAction;
use App\Plugins\RegisterViews;
use App\Vito\Plugins\Flowan\VitoSshLoginNotification\Actions\Disable;
use App\Vito\Plugins\Flowan\VitoSshLoginNotification\Actions\Enable;
use App\Vito\Plugins\Flowan\VitoSshLoginNotification\Http\Controllers\Webhook\SshLoginNotificationController;
use Illuminate\Support\Facades\Route;

class Plugin extends AbstractPlugin
{
    protected string $name = 'SSH Login Notification';

    protected string $description = 'Sends a notification when a user logs in via SSH.';

    public function boot(): void
    {
        RegisterViews::make('vito-ssh-login-notification')
            ->path(__DIR__.'/views')
            ->register();

        RegisterServerFeature::make('ssh-login-notification')
            ->label($this->name)
            ->description($this->description)
            ->register();

        RegisterServerFeatureAction::make('ssh-login-notification', 'enable')
            ->label('Enable')
            ->handler(Enable::class)
            ->register();
        RegisterServerFeatureAction::make('ssh-login-notification', 'disable')
            ->label('Disable')
            ->handler(Disable::class)
            ->register();

        Route::post('webhook/ssh-login-notification/{server}', SshLoginNotificationController::class);
    }
}
