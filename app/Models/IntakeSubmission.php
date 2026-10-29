<?php

namespace App\Models;

use Database\Factories\IntakeSubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['business_name', 'contact_email', 'contact_phone', 'desired_domain', 'about_text', 'services_text', 'colour_preferences', 'extra_notes', 'logo_path', 'photo_paths'])]
class IntakeSubmission extends Model
{
    /** @use HasFactory<IntakeSubmissionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'photo_paths' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
