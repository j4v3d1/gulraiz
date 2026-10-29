<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\Plan;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Subscription;

#[Fillable(['site_template_id', 'plan', 'status', 'commitment_accepted_at', 'live_url', 'admin_notes', 'checklist'])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * Items on the admin's launch checklist for each order.
     *
     * @var array<string, string>
     */
    public const CHECKLIST_ITEMS = [
        'domain_registered' => 'Domain registered',
        'email_created' => 'Business email created',
        'site_deployed' => 'Site deployed',
        'ssl_active' => 'SSL active',
        'backups_scheduled' => 'Backups scheduled',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'plan' => Plan::class,
            'status' => OrderStatus::class,
            'checklist' => 'array',
            'commitment_accepted_at' => 'datetime',
            'commitment_ends_at' => 'datetime',
            'paid_at' => 'datetime',
            'cancellation_requested_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<SiteTemplate, $this>
     */
    public function siteTemplate(): BelongsTo
    {
        return $this->belongsTo(SiteTemplate::class);
    }

    /**
     * @return HasOne<IntakeSubmission, $this>
     */
    public function intakeSubmission(): HasOne
    {
        return $this->hasOne(IntakeSubmission::class);
    }

    /**
     * @return HasMany<EditRequest, $this>
     */
    public function editRequests(): HasMany
    {
        return $this->hasMany(EditRequest::class);
    }

    /**
     * The Cashier subscription that bills this order.
     */
    public function subscription(): ?Subscription
    {
        return $this->subscription_type ? $this->user->subscription($this->subscription_type) : null;
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function isWithinCommitment(): bool
    {
        return $this->commitment_ends_at !== null && $this->commitment_ends_at->isFuture();
    }

    public function acceptsIntake(): bool
    {
        return $this->status === OrderStatus::AwaitingIntake && $this->intakeSubmission === null;
    }

    public function acceptsEditRequests(): bool
    {
        return in_array($this->status, [OrderStatus::ClientReview, OrderStatus::Live], true);
    }

    public function editRequestsUsedThisMonth(): int
    {
        return $this->editRequests()->where('created_at', '>=', now()->startOfMonth())->count();
    }

    public function editRequestsRemainingThisMonth(): int
    {
        return max(0, config('gulraiz.monthly_edit_limit') - $this->editRequestsUsedThisMonth());
    }
}
