<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    public function index()
    {
        // Show tracking form
        return view('track-order');
    }

    public function track(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string'
        ]);

        return redirect()->route('track.show', $request->tracking_number);
    }

    public function show(string $trackingNumber)
    {
        $order = Order::where('tracking_number', $trackingNumber)
                      ->where('user_id', Auth::id())
                      ->first();

        if (!$order) {
            return redirect()->route('track.index')->with('error', 'Tracking number not found.');
        }

        $history = $order->tracking_history ? json_decode($order->tracking_history, true) : [];
        if (empty($history)) {
            $history[] = [
                'status' => $order->tracking_status,
                'date' => $order->updated_at->format('Y-m-d h:i A')
            ];
        }

        return view('track-order', [
            'tracking' => [
                'tracking_number' => $order->tracking_number,
                'status' => $order->tracking_status,
                'history' => $history,
                'order_id' => $order->id,
            ]
        ]);
    }
}
