<?php

namespace App\Services\Admin;

use App\Models\CustomOrder;
use App\Services\Payment\PaymentService;

class CustomOrderService
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function getOrders(array $filters = [])
    {
        $query = CustomOrder::with('user', 'product');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderByDesc('created_at')->paginate(10);
    }

    public function getOrderDetails(CustomOrder $order)
    {
        return $order->load(['user', 'product']);
    }

    public function updateOrderStatus(CustomOrder $order, string $status, array $data = [])
    {
        $validStatuses = ['pending', 'processing', 'in_production', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        $order->status = $status;
        
        if (isset($data['admin_notes'])) {
            $order->admin_notes = $data['admin_notes'];
        }

        if ($status === 'approved') {
            $order->approved_at = now();
        } elseif ($status === 'rejected') {
            $order->rejected_at = now();
            $order->rejection_reason = $data['rejection_reason'] ?? null;
        }

        $order->save();

        return $order;
    }

    public function processPayment(CustomOrder $order, array $paymentData)
    {
        return $this->paymentService->processPayment($order, $paymentData);
    }

    public function getOrderStatistics()
    {
        return [
            'total_orders' => CustomOrder::count(),
            'pending_orders' => CustomOrder::where('status', 'pending')->count(),
            'processing_orders' => CustomOrder::where('status', 'processing')->count(),
            'in_production_orders' => CustomOrder::where('status', 'in_production')->count(),
            'completed_orders' => CustomOrder::where('status', 'completed')->count(),
            'cancelled_orders' => CustomOrder::where('status', 'cancelled')->count(),
            'total_revenue' => CustomOrder::where('status', 'completed')->sum('final_price'),
        ];
    }
}
