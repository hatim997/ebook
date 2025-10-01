<?php

namespace App\Providers;

use App\Events\NotificationEvent;
use App\Models\Notification;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('notificationService', function () {
            return new class {
                public function notifyUsers($users, $title, $message, $tableName = null, $tableId = null, $page = null )
                {
                    foreach ($users as $user) {
                        $notification = Notification::create([
                            'user_id' => $user->id,
                            'title' => $title,
                            'message' => $message,
                            'table_name' => $tableName,
                            'table_id' => $tableId,
                            'page' => $page,
                        ]);
                        // broadcast(new NotificationEvent($notification));
                    }
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
