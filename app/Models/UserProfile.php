<?php
declare(strict_types=1);

namespace App\Models;

class UserProfile extends Model {
    protected string $table = 'user_profiles';
    protected array $fillable = [
        'user_id',
        'headline',
        'bio',
        'phone',
        'avatar_url',
        'language',
        'timezone',
        'theme_preference',
        'email_notifications',
        'security_alerts',
        'created_at',
        'updated_at',
    ];

    public static function forUser(int $userId): self {
        $row = self::query()->where('user_id', $userId)->first();
        if ($row) {
            return new self($row);
        }

        $now = date('Y-m-d H:i:s');
        $created = self::create([
            'user_id' => $userId,
            'headline' => 'Lifelong Learner & Researcher',
            'bio' => '',
            'phone' => '',
            'avatar_url' => '',
            'language' => 'en',
            'timezone' => 'Asia/Colombo',
            'theme_preference' => 'system',
            'email_notifications' => 1,
            'security_alerts' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $created;
    }

    public static function updateForUser(int $userId, array $attributes): self {
        $profile = self::forUser($userId);
        $attributes['updated_at'] = date('Y-m-d H:i:s');
        $profile->fill($attributes);
        $profile->save();
        return $profile;
    }
}
