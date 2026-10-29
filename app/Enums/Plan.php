<?php

namespace App\Enums;

enum Plan: string
{
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::Monthly => __('Monthly'),
            self::Yearly => __('Yearly'),
        };
    }

    /**
     * The Stripe Price ID charged for this plan.
     */
    public function stripePriceId(): ?string
    {
        return config("gulraiz.plans.{$this->value}.price_id");
    }

    /**
     * The pre-tax price in cents, used for display only.
     */
    public function amountInCents(): int
    {
        return config("gulraiz.plans.{$this->value}.amount");
    }

    public function formattedPrice(): string
    {
        return '$'.number_format($this->amountInCents() / 100);
    }

    public function intervalLabel(): string
    {
        return match ($this) {
            self::Monthly => __('month'),
            self::Yearly => __('year'),
        };
    }

    public function hasMinimumCommitment(): bool
    {
        return $this === self::Monthly;
    }
}
