<?php

namespace App\Services\Notification;

use App\Models\CustomOrder;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CustomOrder\PriceQuotedNotification;
use App\Mail\CustomOrder\OrderApprovedNotification;
use App\Mail\CustomOrder\OrderRejectedNotification;
use App\Mail\CustomOrder\OrderCompletedNotification;
use App\Mail\CustomOrder\OrderProcessingNotification;

class CustomOrderNotificationService
{
    /**
     * Send price quoted notification to user
     */
    public function sendPriceQuotedNotification(CustomOrder $order): bool
    {
        try {
            if (!$order->user || !$order->user->email) {
                Log::warning('Cannot send price quoted notification - no user email', [
                    'order_id' => $order->id
                ]);
                return false;
            }

            Mail::to($order->user->email)->send(new PriceQuotedNotification($order));
            
            Log::info('Price quoted notification sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send price quoted notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send order approved notification to user
     */
    public function sendOrderApprovedNotification(CustomOrder $order): bool
    {
        try {
            if (!$order->user || !$order->user->email) {
                Log::warning('Cannot send order approved notification - no user email', [
                    'order_id' => $order->id
                ]);
                return false;
            }

            Mail::to($order->user->email)->send(new OrderApprovedNotification($order));
            
            Log::info('Order approved notification sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order approved notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send order rejected notification to user
     */
    public function sendOrderRejectedNotification(CustomOrder $order): bool
    {
        try {
            if (!$order->user || !$order->user->email) {
                Log::warning('Cannot send order rejected notification - no user email', [
                    'order_id' => $order->id
                ]);
                return false;
            }

            Mail::to($order->user->email)->send(new OrderRejectedNotification($order));
            
            Log::info('Order rejected notification sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order rejected notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send order processing notification to user
     */
    public function sendOrderProcessingNotification(CustomOrder $order): bool
    {
        try {
            if (!$order->user || !$order->user->email) {
                Log::warning('Cannot send order processing notification - no user email', [
                    'order_id' => $order->id
                ]);
                return false;
            }

            Mail::to($order->user->email)->send(new OrderProcessingNotification($order));
            
            Log::info('Order processing notification sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order processing notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send order completed notification to user
     */
    public function sendOrderCompletedNotification(CustomOrder $order): bool
    {
        try {
            if (!$order->user || !$order->user->email) {
                Log::warning('Cannot send order completed notification - no user email', [
                    'order_id' => $order->id
                ]);
                return false;
            }

            Mail::to($order->user->email)->send(new OrderCompletedNotification($order));
            
            Log::info('Order completed notification sent', [
                'order_id' => $order->id,
                'user_email' => $order->user->email
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send order completed notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send new order notification to admin
     */
    public function sendNewOrderNotificationToAdmin(CustomOrder $order): bool
    {
        try {
            $adminEmail = config('mail.admin_email', 'admin@yakan-ecommerce.com');
            
            Mail::to($adminEmail)->send(new \App\Mail\CustomOrder\NewOrderNotification($order));
            
            Log::info('New order notification sent to admin', [
                'order_id' => $order->id,
                'admin_email' => $adminEmail
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send new order notification to admin', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notification in queue (non-blocking)
     */
    public function queueNotification(string $notificationType, CustomOrder $order): void
    {
        try {
            $methodName = 'send' . str_replace(' ', '', ucwords(str_replace('_', ' ', $notificationType))) . 'Notification';
            
            if (method_exists($this, $methodName)) {
                // Queue the notification to avoid blocking the request
                dispatch(function () use ($methodName, $order) {
                    $this->$methodName($order);
                })->afterResponse();
            } else {
                Log::warning('Unknown notification type', [
                    'type' => $notificationType,
                    'order_id' => $order->id
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to queue notification', [
                'type' => $notificationType,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
