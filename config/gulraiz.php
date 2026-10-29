<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Contact
    |--------------------------------------------------------------------------
    |
    | Where notifications about new orders, intake forms, edit requests,
    | failed payments and contact messages are sent.
    |
    */

    'admin_email' => env('GULRAIZ_ADMIN_EMAIL', 'hello@gulraiz.ca'),

    /*
    |--------------------------------------------------------------------------
    | Plans
    |--------------------------------------------------------------------------
    |
    | Every template sells for the same price. Amounts are in cents (CAD)
    | and are only used for display; Stripe Prices are the source of truth
    | for what is actually charged.
    |
    */

    'plans' => [
        'monthly' => [
            'price_id' => env('STRIPE_PRICE_MONTHLY'),
            'amount' => 5000,
            'interval' => 'month',
        ],
        'yearly' => [
            'price_id' => env('STRIPE_PRICE_YEARLY'),
            'amount' => 50000,
            'interval' => 'year',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Minimum Commitment
    |--------------------------------------------------------------------------
    |
    | Monthly subscribers agree to this many months at checkout. A
    | cancellation requested inside the term takes effect when it ends.
    |
    */

    'commitment_months' => (int) env('GULRAIZ_COMMITMENT_MONTHS', 12),

    /*
    |--------------------------------------------------------------------------
    | Content Edits
    |--------------------------------------------------------------------------
    */

    'monthly_edit_limit' => (int) env('GULRAIZ_MONTHLY_EDIT_LIMIT', 10),

];
