<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card'
        ]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    public function test_order_has_many_order_items(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card'
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $order->orderItems);
    }

    public function test_order_fillable_attributes(): void
    {
        $user = User::factory()->create();

        $orderData = [
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'tracking_number' => 'TRACK123',
            'tracking_status' => 'in_transit'
        ];

        $order = Order::create($orderData);

        foreach ($orderData as $key => $value) {
            $this->assertEquals($value, $order->$key);
        }
    }

    public function test_tracking_history_getter_handles_json_string(): void
    {
        $user = User::factory()->create();

        $trackingHistory = json_encode([
            ['status' => 'order_placed', 'date' => '2023-01-01 10:00 AM'],
            ['status' => 'processing', 'date' => '2023-01-02 02:00 PM']
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'tracking_history' => $trackingHistory
        ]);

        $history = $order->tracking_history;
        $this->assertIsArray($history);
        $this->assertCount(2, $history);
        $this->assertEquals('order_placed', $history[0]['status']);
    }

    public function test_tracking_history_getter_handles_escaped_json(): void
    {
        $user = User::factory()->create();

        $trackingHistory = json_encode([
            ['status' => 'order_placed', 'date' => '2023-01-01 10:00 AM']
        ]);

        $escapedHistory = json_encode($trackingHistory);

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'tracking_history' => $escapedHistory
        ]);

        $history = $order->tracking_history;
        $this->assertIsArray($history);
        $this->assertCount(1, $history);
        $this->assertEquals('order_placed', $history[0]['status']);
    }

    public function test_tracking_history_getter_returns_empty_array_for_null(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card',
            'tracking_history' => null
        ]);

        $history = $order->tracking_history;
        $this->assertIsArray($history);
        $this->assertEmpty($history);
    }

    public function test_append_tracking_event(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card'
        ]);

        $order->appendTrackingEvent('order_placed', '2023-01-01 10:00 AM');
        $order->save();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'tracking_history' => json_encode([
                ['status' => 'order_placed', 'date' => '2023-01-01 10:00 AM']
            ])
        ]);
    }

    public function test_append_tracking_event_with_default_date(): void
    {
        $user = User::factory()->create();

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'credit_card'
        ]);

        $order->appendTrackingEvent('processing');
        $order->save();

        $history = $order->fresh()->tracking_history;
        $this->assertCount(1, $history);
        $this->assertEquals('processing', $history[0]['status']);
        $this->assertArrayHasKey('date', $history[0]);
    }

    public function test_append_tracking_event_adds_to_existing_history(): void
    {
        $user = User::factory()->create();

        $existingHistory = [
            ['status' => 'order_placed', 'date' => '2023-01-01 10:00 AM']
        ];

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 199.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'credit_card',
            'tracking_history' => json_encode($existingHistory)
        ]);

        $order->appendTrackingEvent('shipped', '2023-01-03 02:00 PM');
        $order->save();

        $history = $order->fresh()->tracking_history;
        $this->assertCount(2, $history);
        $this->assertEquals('order_placed', $history[0]['status']);
        $this->assertEquals('shipped', $history[1]['status']);
    }
}
