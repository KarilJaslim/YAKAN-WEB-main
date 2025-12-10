<?php

namespace App\Services\CustomOrder;

use App\Models\CustomOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CustomOrderValidationService
{
    /**
     * Validate custom order creation data
     */
    public function validateCreateOrder(array $data): array
    {
        $validator = Validator::make($data, [
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
        ], [
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
            'accent_color.regex' => 'Color must be in hex format (e.g., #FF0000).'
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ];
        }

        // Business logic validations
        $businessValidation = $this->validateBusinessRules($data);
        if (!$businessValidation['valid']) {
            return $businessValidation;
        }

        return ['valid' => true];
    }

    /**
     * Validate custom order update data
     */
    public function validateUpdateOrder(array $data): array
    {
        $validator = Validator::make($data, [
            'specifications' => 'nullable|string|max:2000',
            'quantity' => 'sometimes|integer|min:1|max:100',
            'budget_range' => 'nullable|string|max:100',
            'expected_date' => 'nullable|date|after:today|after:+1 week',
            'primary_color' => 'nullable|string|max:50|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|max:50|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|max:50|regex:/^#[0-9A-Fa-f]{6}$/',
            'dimensions' => 'nullable|string|max:255',
            'phone' => 'sometimes|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'email' => 'sometimes|email|max:255',
            'delivery_address' => 'sometimes|string|max:500|min:10',
            'additional_notes' => 'nullable|string|max:1000'
        ], [
            'quantity.max' => 'Maximum quantity allowed is 100 items.',
            'expected_date.after' => 'Expected delivery date must be at least 1 week from today.',
            'phone.regex' => 'Please enter a valid phone number.',
            'delivery_address.min' => 'Delivery address must be at least 10 characters long.',
            'primary_color.regex' => 'Color must be in hex format (e.g., #FF0000).',
            'secondary_color.regex' => 'Color must be in hex format (e.g., #FF0000).',
            'accent_color.regex' => 'Color must be in hex format (e.g., #FF0000).'
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate pricing estimate request
     */
    public function validatePricingEstimate(array $data): array
    {
        $validator = Validator::make($data, [
            'product_type' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1|max:100',
            'dimensions' => 'nullable|string|max:255',
            'complexity' => 'nullable|in:low,medium,high'
        ], [
            'quantity.max' => 'Maximum quantity allowed is 100 items.',
            'complexity.in' => 'Complexity must be low, medium, or high.'
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate file upload
     */
    public function validateFileUpload($file): array
    {
        $validator = Validator::make(['file' => $file], [
            'file' => [
                'required',
                'file',
                'mimes:' . env('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,pdf,doc,docx'),
                'max:' . env('MAX_FILE_SIZE', 5120)
            ]
        ], [
            'file.mimes' => 'File must be one of: jpg, jpeg, png, pdf, doc, docx',
            'file.max' => 'File size must not exceed 5MB'
        ]);

        if ($validator->fails()) {
            return [
                'valid' => false,
                'errors' => $validator->errors(),
                'message' => 'File validation failed'
            ];
        }

        // Additional file content validation
        $contentValidation = $this->validateFileContent($file);
        if (!$contentValidation['valid']) {
            return $contentValidation;
        }

        return ['valid' => true];
    }

    /**
     * Validate order cancellation
     */
    public function validateOrderCancellation(CustomOrder $order): array
    {
        if ($order->user_id !== Auth::id()) {
            return [
                'valid' => false,
                'message' => 'You can only cancel your own orders'
            ];
        }

        if ($order->isCancelled()) {
            return [
                'valid' => false,
                'message' => 'Order is already cancelled'
            ];
        }

        if ($order->isCompleted()) {
            return [
                'valid' => false,
                'message' => 'Cannot cancel completed orders'
            ];
        }

        if ($order->status === 'processing') {
            return [
                'valid' => false,
                'message' => 'Cannot cancel orders that are already in production'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate price acceptance
     */
    public function validatePriceAcceptance(CustomOrder $order): array
    {
        if ($order->user_id !== Auth::id()) {
            return [
                'valid' => false,
                'message' => 'You can only accept prices for your own orders'
            ];
        }

        if (!$order->isAwaitingUserDecision()) {
            return [
                'valid' => false,
                'message' => 'This order is not ready for price acceptance'
            ];
        }

        if (!$order->final_price) {
            return [
                'valid' => false,
                'message' => 'No price has been quoted for this order'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate price rejection
     */
    public function validatePriceRejection(CustomOrder $order): array
    {
        if ($order->user_id !== Auth::id()) {
            return [
                'valid' => false,
                'message' => 'You can only reject prices for your own orders'
            ];
        }

        if (!$order->isAwaitingUserDecision()) {
            return [
                'valid' => false,
                'message' => 'This order is not ready for price rejection'
            ];
        }

        return ['valid' => true];
    }

    /**
     * Validate business rules
     */
    private function validateBusinessRules(array $data): array
    {
        // Check if user has too many pending orders
        $pendingCount = CustomOrder::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        if ($pendingCount >= 5) {
            return [
                'valid' => false,
                'message' => 'You have reached the maximum number of pending orders (5). Please wait for some orders to be processed.'
            ];
        }

        // Check if quantity is reasonable
        $quantity = $data['quantity'] ?? 1;
        if ($quantity > 50) {
            return [
                'valid' => false,
                'message' => 'For orders over 50 items, please contact us directly for bulk pricing.'
            ];
        }

        // Validate expected date is not too far in future
        if (isset($data['expected_date'])) {
            $expectedDate = \Carbon\Carbon::parse($data['expected_date']);
            $maxDate = \Carbon\Carbon::now()->addMonths(6);
            
            if ($expectedDate->gt($maxDate)) {
                return [
                    'valid' => false,
                    'message' => 'Expected delivery date cannot be more than 6 months in the future.'
                ];
            }
        }

        // Validate budget range format if provided
        if (isset($data['budget_range'])) {
            $budgetRange = $data['budget_range'];
            if (!preg_match('/^(\d+)-(\d+)$/', $budgetRange) && !preg_match('/^Above \d+$/', $budgetRange)) {
                return [
                    'valid' => false,
                    'message' => 'Budget range must be in format "1000-5000" or "Above 5000"'
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Validate file content for security
     */
    private function validateFileContent($file): array
    {
        // Check file size again (double check)
        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
            return [
                'valid' => false,
                'message' => 'File size exceeds maximum limit'
            ];
        }

        // Check for suspicious file extensions in filename
        $filename = $file->getClientOriginalName();
        $suspiciousExtensions = ['exe', 'bat', 'cmd', 'scr', 'pif', 'com'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($extension, $suspiciousExtensions)) {
            return [
                'valid' => false,
                'message' => 'File type not allowed'
            ];
        }

        // Additional MIME type verification
        $allowedMimes = [
            'image/jpeg', 'image/jpg', 'image/png', 
            'application/pdf', 
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return [
                'valid' => false,
                'message' => 'File type verification failed'
            ];
        }

        return ['valid' => true];
    }
}
