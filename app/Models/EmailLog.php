<?php
declare(strict_types=1);

namespace App\Models;

class EmailLog extends Model {
    protected string $table = 'email_logs';
    protected array $fillable = [
        'to_email',
        'to_name',
        'subject',
        'body_html',
        'status',
        'sent_at',
        'created_at',
        'updated_at',
    ];
}
