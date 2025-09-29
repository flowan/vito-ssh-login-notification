<?php

namespace App\Vito\Plugins\Flowan\VitoSshLoginNotification\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\NotificationChannel;
use App\Models\Server;
use App\Vito\Plugins\Flowan\VitoSshLoginNotification\Notifications\ServerSshLogin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SshLoginNotificationController extends Controller
{
    public function __invoke(int $server, Request $request): JsonResponse
    {
        $server = Server::query()->findOrFail($server);

        if (
            isset($server->feature_data['ssh_login_notification'])
            && $server->feature_data['ssh_login_notification'] === true
            && $server->feature_data['ssh_login_notification_token'] === $request->bearerToken()
        ) {
            $notificationChannel = NotificationChannel::query()->findOrFail($server->feature_data['ssh_login_notification_channel']);
            $notificationChannel->notify(new ServerSshLogin(
                $server,
                $request->json('user'),
                Str::replace('_', '.', $request->json('ip')),
                now(),
            ));
        }

        return response()->json();
    }
}
