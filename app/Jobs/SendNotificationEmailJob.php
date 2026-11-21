<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Queue\JobInterface;
use App\Services\NotificationService;

class SendNotificationEmailJob implements JobInterface {
    public int $userId;
    public string $type;
    public string $title;
    public string $message;
    public ?string $actionUrl;

    public function __construct(int $userId, string $type, string $title, string $message, ?string $actionUrl = null) {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
    }

    public function handle(): void {
        NotificationService::send($this->userId, $this->type, $this->title, $this->message, $this->actionUrl);
    }
}
