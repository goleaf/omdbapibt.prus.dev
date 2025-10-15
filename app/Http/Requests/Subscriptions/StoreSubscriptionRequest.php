<?php

namespace App\Http\Requests\Subscriptions;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'price' => ['required', 'string'],
        ];
    }

    /**
     * Get the validation messages for defined rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.required' => __('subscriptions.validation.price_required'),
        ];
    }

    public function price(): string
    {
        /** @var array{price: string} $validated */
        $validated = $this->validated();

        return $validated['price'];
    }
}
