<?php

namespace App\Enums;

enum EditRequestStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('Open'),
            self::InProgress => __('In progress'),
            self::Done => __('Done'),
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-amber-100 text-amber-800',
            self::InProgress => 'bg-indigo-100 text-indigo-800',
            self::Done => 'bg-emerald-100 text-emerald-800',
        };
    }
}
