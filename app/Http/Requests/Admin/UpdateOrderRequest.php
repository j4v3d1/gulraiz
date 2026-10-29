<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('admin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(OrderStatus::class)->only(OrderStatus::adminSelectable())],
            'live_url' => ['nullable', 'url:https,http', 'max:255'],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
            'checklist' => ['nullable', 'array'],
            'checklist.*' => [Rule::in(array_keys(Order::CHECKLIST_ITEMS))],
        ];
    }
}
