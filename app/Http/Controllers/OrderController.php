<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Http\Response;
use Midtrans\Snap;
use Midtrans\Config as MidtransConfig;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($user->role === 'admin') {
            $orders = Order::with('package')->orderBy('created_at', 'desc')->get();
        } else {
            $orders = Order::with('package')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }
        return response()->json($orders);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            // 'user_id' dihapus dari validasi
            'package_id' => 'required|integer|exists:packages,id',
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'midtrans_order_id' => 'nullable|string',
            'total_price' => 'required|numeric|min:0',
        ]);
        $validated['user_id'] = $user->id;
        $order = Order::create($validated);
        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'package_id' => 'sometimes|required|integer|exists:packages,id',
            'name' => 'sometimes|required|string|max:255',
            'contact' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'location' => 'sometimes|required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
            'payment_status' => 'nullable|string',
            'midtrans_order_id' => 'nullable|string',
            'total_price' => 'sometimes|required|numeric|min:0',
        ]);
        $order->update($validated);
        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully.']);
    }

    /**
     * Generate Midtrans Snap Token for an order
     */
    public function createMidtransToken($id)
    {
        $order = Order::with('package')->findOrFail($id);
        // Konfigurasi Midtrans
        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = config('services.midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('services.midtrans.is_3ds');

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->name,
                'phone' => $order->contact,
            ],
            'item_details' => [
                [
                    'id' => $order->package->id,
                    'price' => (int) $order->total_price,
                    'quantity' => 1,
                    'name' => $order->package->name,
                ]
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            // Simpan snap_token ke order jika mau (opsional)
            $order->midtrans_order_id = $params['transaction_details']['order_id'];
            $order->save();
            return response()->json(['token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle Midtrans payment notification (webhook)
     */
    public function midtransNotification(Request $request)
    {
        // Konfigurasi Midtrans
        MidtransConfig::$serverKey = config('services.midtrans.server_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production');
        MidtransConfig::$isSanitized = config('services.midtrans.is_sanitized');
        MidtransConfig::$is3ds = config('services.midtrans.is_3ds');

        $notif = new \Midtrans\Notification();
        $orderId = $notif->order_id;
        $transaction = $notif->transaction_status;
        $fraud = $notif->fraud_status;

        // Cari order berdasarkan midtrans_order_id
        $order = Order::where('midtrans_order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Update status pembayaran
        if ($transaction == 'capture') {
            if ($fraud == 'challenge') {
                $order->payment_status = 'challenge';
            } else if ($fraud == 'accept') {
                $order->payment_status = 'paid';
                $order->status = 'confirmed';
            }
        } else if ($transaction == 'settlement') {
            $order->payment_status = 'paid';
            $order->status = 'confirmed';
        } else if ($transaction == 'pending') {
            $order->payment_status = 'pending';
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $order->payment_status = 'failed';
            $order->status = 'cancelled';
        }
        $order->save();

        return response()->json(['message' => 'Notification processed']);
    }

    // Statistik order per bulan (12 bulan terakhir)
    public function ordersPerMonth()
    {
        $orders = Order::selectRaw('YEAR(date) as year, MONTH(date) as month, COUNT(*) as total')
            ->where('date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Siapkan array 12 bulan terakhir
        $labels = [];
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M');
            $found = $orders->first(fn($o) => $o->year == $date->year && $o->month == $date->month);
            $data[] = $found ? (int) $found->total : 0;
        }
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    // Statistik dashboard lengkap
    public function dashboardStats()
    {
        $total_users = \App\Models\User::count();
        $total_orders = Order::count();
        $total_packages = \App\Models\Package::count();
        $total_portfolios = \App\Models\Portfolio::count();

        $latest_orders = Order::orderBy('created_at', 'desc')->take(5)->get();
        $latest_users = \App\Models\User::orderBy('created_at', 'desc')->take(5)->get();

        $notifications = [
            [
                'type' => 'order',
                'message' => 'Order baru masuk',
                'time' => '5 menit yang lalu',
            ],
            [
                'type' => 'user',
                'message' => 'User baru mendaftar',
                'time' => '10 menit yang lalu',
            ],
            [
                'type' => 'payment',
                'message' => 'Pembayaran diterima',
                'time' => '30 menit yang lalu',
            ],
        ];

        return response()->json([
            'total_users' => $total_users,
            'total_orders' => $total_orders,
            'total_packages' => $total_packages,
            'total_portfolios' => $total_portfolios,
            'latest_orders' => $latest_orders,
            'latest_users' => $latest_users,
            'notifications' => $notifications,
        ]);
    }
}
