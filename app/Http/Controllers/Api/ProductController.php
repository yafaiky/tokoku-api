<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Menampilkan semua produk aktif dengan pagination dan fitur pencarian & filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category')->where('is_active', true);

        // BONUS: Fitur pencarian berdasarkan nama produk
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // BONUS: Filter berdasarkan kategori
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data'    => $products,
        ], 200);
    }

    /**
     * Membuat produk baru.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Auto-generate slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $validated['slug'] = $slug;

        $product = Product::create($validated);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dibuat',
            'data'    => $product,
        ], 201);
    }

    /**
     * Menampilkan detail produk beserta kategori.
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil',
            'data'    => $product,
        ], 200);
    }

    /**
     * Memperbarui data produk.
     */
    public function update(StoreProductRequest $request, int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $validated = $request->validated();

        // Update slug jika nama berubah
        if ($product->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $product->update($validated);
        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data'    => $product,
        ], 200);
    }

    /**
     * Toggle status is_active produk (aktif / nonaktif).
     */
    public function toggle(int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $product->update(['is_active' => !$product->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status produk berhasil diubah menjadi ' . ($product->is_active ? 'aktif' : 'nonaktif'),
            'data'    => [
                'id'        => $product->id,
                'name'      => $product->name,
                'is_active' => $product->is_active,
            ],
        ], 200);
    }

    /**
     * Menghapus produk.
     */
    public function destroy(int $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus',
        ], 200);
    }
}
