<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'product_type' => 'required|string|max:255',
            'specifications' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:1|max:100',
            'budget_range' => 'nullable|string|max:100',
            'expected_date' => 'nullable|date|after:today|after:+1 week',
            'primary_color' => 'nullable|string|max:50|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|max:50|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|max:50|regex:/^#[0-9A-Fa-f]{6}$/',
            'dimensions' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'email' => 'required|email|max:255',
            'delivery_address' => 'required|string|max:500|min:10',
            'additional_notes' => 'nullable|string|max:1000',
            'design_upload' => 'nullable|string|max:255',
            'patterns' => 'nullable|array|max:10',
            'patterns.*.name' => 'required|string|max:255',
            'patterns.*.description' => 'nullable|string|max:500',
            'product_ids' => 'nullable|array|max:5',
            'product_ids.*' => 'exists:products,id'
        ];

        // Add file validation for file uploads
        if ($this->hasFile('design_upload')) {
            $rules['design_upload'] = [
                'nullable',
                'file',
                'mimes:' . env('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,pdf,doc,docx'),
                'max:' . env('MAX_FILE_SIZE', 5120)
            ];
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'product_type.required' => 'Please specify the product type you want to customize.',
            'quantity.required' => 'Please specify the quantity you need.',
            'quantity.max' => 'Maximum quantity allowed is 100 items.',
            'expected_date.after' => 'Expected delivery date must be at least 1 week from today.',
            'phone.regex' => 'Please enter a valid phone number.',
            'delivery_address.min' => 'Delivery address must be at least 10 characters long.',
            'patterns.max' => 'Maximum 10 patterns allowed.',
            'product_ids.max' => 'Maximum 5 products can be referenced.',
            'primary_color.regex' => 'Color must be in hex format (e.g., #FF0000).',
            'secondary_color.regex' => 'Color must be in hex format (e.g., #FF0000).',
            'accent_color.regex' => 'Color must be in hex format (e.g., #FF0000).',
            'design_upload.mimes' => 'File must be one of: jpg, jpeg, png, pdf, doc, docx',
            'design_upload.max' => 'File size must not exceed 5MB'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_type' => 'product type',
            'specifications' => 'specifications',
            'quantity' => 'quantity',
            'budget_range' => 'budget range',
            'expected_date' => 'expected delivery date',
            'primary_color' => 'primary color',
            'secondary_color' => 'secondary color',
            'accent_color' => 'accent color',
            'dimensions' => 'dimensions',
            'phone' => 'phone number',
            'email' => 'email address',
            'delivery_address' => 'delivery address',
            'additional_notes' => 'additional notes',
            'design_upload' => 'design file',
            'patterns' => 'patterns',
            'product_ids' => 'products'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if user has too many pending orders
            if (Auth::check()) {
                $pendingCount = \App\Models\CustomOrder::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->count();

                if ($pendingCount >= 5) {
                    $validator->errors()->add('quantity', 
                        'You have reached the maximum number of pending orders (5). Please wait for some orders to be processed.'
                    );
                }
            }

            // Validate budget range format if provided
            if ($this->filled('budget_range')) {
                $budgetRange = $this->input('budget_range');
                if (!preg_match('/^(\d+)-(\d+)$/', $budgetRange) && !preg_match('/^Above \d+$/', $budgetRange)) {
                    $validator->errors()->add('budget_range', 
                        'Budget range must be in format "1000-5000" or "Above 5000"'
                    );
                }
            }

            // Validate expected date is not too far in future
            if ($this->filled('expected_date')) {
                $expectedDate = \Carbon\Carbon::parse($this->input('expected_date'));
                $maxDate = \Carbon\Carbon::now()->addMonths(6);
                
                if ($expectedDate->gt($maxDate)) {
                    $validator->errors()->add('expected_date', 
                        'Expected delivery date cannot be more than 6 months in the future.'
                    );
                }
            }

            // Check if quantity is reasonable for bulk orders
            $quantity = $this->input('quantity', 1);
            if ($quantity > 50) {
                $validator->errors()->add('quantity', 
                    'For orders over 50 items, please contact us directly for bulk pricing.'
                );
            }
        });
    }
}
