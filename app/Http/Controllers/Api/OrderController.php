<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan semua pesanan milik user yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data pesanan berhasil diambil',
            'data'    => $orders,
        ], 200);
    }

    /**
     * Membuat pesanan baru dengan array item produk.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Mulai database transaction untuk memastikan konsistensi data
        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $itemsToCreate = [];

            // Validasi stok dan hitung total harga
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                // Cek apakah produk aktif
                if (!$product->is_active) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Produk '{$product->name}' tidak tersedia.",
                    ], 400);
                }

                // Cek ketersediaan stok
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stok produk '{$product->name}' tidak mencukupi. Stok tersedia: {$product->stock}.",
                    ], 400);
                }

                $subtotal     = $product->price * $item['quantity'];
                $totalPrice  += $subtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ];

                // Kurangi stok produk
                $product->decrement('stock', $item['quantity']);
            }

            // Buat order
            $order = Order::create([
                'user_id'     => $request->user()->id,
                'total_price' => $totalPrice,
                'status'      => 'pending',
                'notes'       => $validated['notes'] ?? null,
            ]);

            // Buat order items
            foreach ($itemsToCreate as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }

            DB::commit();

            // Load relasi untuk respons
            $order->load('items.product');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data'    => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Menampilkan detail pesanan beserta item-itemnya.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::with(['items.product', 'user'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        // Pastikan pesanan milik user yang login
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pesanan ini',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pesanan berhasil diambil',
            'data'    => $order,
        ], 200);
    }

    /**
     * Memperbarui status pesanan.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        // Pastikan pesanan milik user yang login
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke pesanan ini',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,done,cancelled',
        ], [
            'status.required' => 'Status pesanan wajib diisi.',
            'status.in'       => 'Status tidak valid. Pilihan: pending, processing, done, cancelled.',
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status pesanan berhasil diperbarui',
            'data'    => $order,
        ], 200);
    }
}
