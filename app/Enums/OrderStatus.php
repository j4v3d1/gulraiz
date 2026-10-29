<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PendingPayment = 'pending_payment';
    case AwaitingIntake = 'awaiting_intake';
    case Building = 'building';
    case ClientReview = 'client_review';
    case Live = 'live';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => __('Pending payment'),
            self::AwaitingIntake => __('Waiting for your details'),
            self::Building => __('Building'),
            self::ClientReview => __('Ready for your review'),
            self::Live => __('Live'),
            self::Cancelled => __('Cancelled'),
        };
    }

    /**
     * Tailwind classes for the status badge.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::PendingPayment => 'bg-slate-100 text-slate-700',
            self::AwaitingIntake => 'bg-amber-100 text-amber-800',
            self::Building => 'bg-indigo-100 text-indigo-800',
            self::ClientReview => 'bg-sky-100 text-sky-800',
            self::Live => 'bg-emerald-100 text-emerald-800',
            self::Cancelled => 'bg-rose-100 text-rose-800',
        };
    }

    /**
     * Statuses an admin can move a paid order into.
     *
     * @return list<self>
     */
    public static function adminSelectable(): array
    {
        return [self::AwaitingIntake, self::Building, self::ClientReview, self::Live, self::Cancelled];
    }
}
