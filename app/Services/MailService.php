<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\EmailLog;
use App\Security\SecurityAuditLog;

class MailService {
    /** @var array<int, array> Static in-memory mail bag for testing & simulation */
    private static array $sentMails = [];

    public static function send(
        string $toEmail,
        ?string $toName,
        string $subject,
        string $htmlBody,
        ?string $textBody = null
    ): bool {
        $now = date('Y-m-d H:i:s');
        $fromConfig = config('mail.from', [
            'address' => 'noreply@shilpa-lms.edu',
            'name' => 'SHILPA LMS Academic Portal',
        ]);

        // Record in in-memory test bag
        self::$sentMails[] = [
            'to_email' => $toEmail,
            'to_name' => $toName,
            'subject' => $subject,
            'html' => $htmlBody,
            'text' => $textBody,
            'sent_at' => $now,
        ];

        // Persist to database email_logs table
        try {
            EmailLog::create([
                'to_email' => $toEmail,
                'to_name' => $toName,
                'subject' => $subject,
                'body_html' => $htmlBody,
                'status' => 'sent',
                'sent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (\Throwable $e) {
            // Log fallback if DB table not yet migrated during raw unit tests
            error_log('Failed to log email: ' . $e->getMessage());
        }

        SecurityAuditLog::log('communication.email.dispatched', 'info', null, null, [
            'recipient' => $toEmail,
            'subject' => $subject,
        ]);

        return true;
    }

    public static function buildTemplate(
        string $title,
        string $greeting,
        string $bodyContent,
        ?string $ctaText = null,
        ?string $ctaUrl = null
    ): string {
        $appName = config('app.name', 'SHILPA LMS');
        $buttonHtml = '';
        if ($ctaText && $ctaUrl) {
            $buttonHtml = <<<HTML
            <div style="margin: 28px 0; text-align: center;">
                <a href="{$ctaUrl}" style="background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-block;">
                    {$ctaText}
                </a>
            </div>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$title}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0f172a; color: #f8fafc; margin: 0; padding: 30px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #1e293b; border-radius: 12px; border: 1px solid #334155; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #4f46e5, #7c3aed); padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; font-size: 20px; font-weight: 800; margin: 0; letter-spacing: -0.02em;">{$appName}</h1>
            <p style="color: #e0e7ff; font-size: 12px; margin: 4px 0 0; text-transform: uppercase; letter-spacing: 0.05em;">Academic Notification Dispatch</p>
        </div>
        <div style="padding: 32px 28px;">
            <h2 style="color: #ffffff; font-size: 18px; margin: 0 0 16px; font-weight: 700;">{$greeting}</h2>
            <div style="color: #cbd5e1; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                {$bodyContent}
            </div>
            {$buttonHtml}
            <div style="border-top: 1px solid #334155; padding-top: 20px; margin-top: 28px; font-size: 12px; color: #64748b; line-height: 1.5;">
                <p style="margin: 0;">This is an automated notification from {$appName}. You can adjust your communication preferences in your profile settings.</p>
                <p style="margin: 4px 0 0;">© 2026 {$appName} • Modern Academic Architecture</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    public static function getSentMails(): array {
        return self::$sentMails;
    }

    public static function clearSentMails(): void {
        self::$sentMails = [];
    }
}
