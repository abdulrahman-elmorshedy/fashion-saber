<?php
namespace InnoShop\Common\Services\Notification;

interface NotificationChannelInterface
{
    /**
     * Send a notification through this channel.
     *
     * @param  string  $title  Notification title
     * @param  string  $content  Notification content (markdown supported)
     * @param  string  $level  Level: info, warning, error
     * @return void
     */
    public function send(string $title, string $content, string $level = 'info'): void;
}
