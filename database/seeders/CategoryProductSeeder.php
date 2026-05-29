<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ================================================
        // Buat 3 Kategori
        // ================================================
        $elektronik = Category::create([
            'name'        => 'Elektronik',
            'slug'        => 'elektronik',
            'description' => 'Perangkat elektronik dan gadget terbaru',
        ]);

        $fashion = Category::create([
            'name'        => 'Fashion',
            'slug'        => 'fashion',
            'description' => 'Pakaian, sepatu, dan aksesoris fashion',
        ]);

        $makanan = Category::create([
            'name'        => 'Makanan & Minuman',
            'slug'        => 'makanan-minuman',
            'description' => 'Produk makanan dan minuman segar pilihan',
        ]);

        // ================================================
        // Buat 10 Produk (masing-masing tersebar ke kategori)
        // ================================================
        $products = [
            // Kategori Elektronik (4 produk)
            [
                'category_id' => $elektronik->id,
                'name'        => 'Smartphone Samsung Galaxy A55',
                'slug'        => 'smartphone-samsung-galaxy-a55',
                'description' => 'Smartphone flagship dengan layar AMOLED 6.6 inch, RAM 8GB, Kamera 50MP',
                'price'       => 4599000,
                'stock'       => 25,
                'is_active'   => true,
            ],
            [
                'category_id' => $elektronik->id,
                'name'        => 'Laptop ASUS VivoBook 15',
                'slug'        => 'laptop-asus-vivobook-15',
                'description' => 'Laptop ringan dengan Intel Core i5 Gen 12, RAM 16GB, SSD 512GB',
                'price'       => 8750000,
                'stock'       => 10,
                'is_active'   => true,
            ],
            [
                'category_id' => $elektronik->id,
                'name'        => 'TWS Earbuds JBL Wave 300',
                'slug'        => 'tws-earbuds-jbl-wave-300',
                'description' => 'Earbuds wireless dengan baterai 6 jam, suara bass yang kuat',
                'price'       => 399000,
                'stock'       => 50,
                'is_active'   => true,
            ],
            [
                'category_id' => $elektronik->id,
                'name'        => 'Charger USB-C 65W GaN',
                'slug'        => 'charger-usb-c-65w-gan',
                'description' => 'Charger cepat GaN 65W compatible dengan laptop, hp, dan tablet',
                'price'       => 285000,
                'stock'       => 100,
                'is_active'   => true,
            ],

            // Kategori Fashion (3 produk)
            [
                'category_id' => $fashion->id,
                'name'        => 'Kaos Polos Premium Cotton',
                'slug'        => 'kaos-polos-premium-cotton',
                'description' => 'Kaos katun combed 30s, nyaman dipakai seharian, tersedia 10 warna',
                'price'       => 85000,
                'stock'       => 200,
                'is_active'   => true,
            ],
            [
                'category_id' => $fashion->id,
                'name'        => 'Sepatu Sneakers Casual Pria',
                'slug'        => 'sepatu-sneakers-casual-pria',
                'description' => 'Sepatu sneakers dengan sol karet anti-selip, material canvas berkualitas',
                'price'       => 320000,
                'stock'       => 45,
                'is_active'   => true,
            ],
            [
                'category_id' => $fashion->id,
                'name'        => 'Tas Ransel Anti-Air 30L',
                'slug'        => 'tas-ransel-anti-air-30l',
                'description' => 'Ransel waterproof kapasitas 30L, cocok untuk kerja dan travelling',
                'price'       => 450000,
                'stock'       => 30,
                'is_active'   => true,
            ],

            // Kategori Makanan & Minuman (3 produk)
            [
                'category_id' => $makanan->id,
                'name'        => 'Kopi Arabika Gayo 250gr',
                'slug'        => 'kopi-arabika-gayo-250gr',
                'description' => 'Biji kopi Arabika asli dari dataran tinggi Gayo, Aceh. Roast medium.',
                'price'       => 75000,
                'stock'       => 80,
                'is_active'   => true,
            ],
            [
                'category_id' => $makanan->id,
                'name'        => 'Cokelat Dark Chocolate 70%',
                'slug'        => 'cokelat-dark-chocolate-70',
                'description' => 'Cokelat hitam premium dengan kandungan kakao 70%, rendah gula',
                'price'       => 45000,
                'stock'       => 120,
                'is_active'   => true,
            ],
            [
                'category_id' => $makanan->id,
                'name'        => 'Madu Hutan Murni 500ml',
                'slug'        => 'madu-hutan-murni-500ml',
                'description' => 'Madu hutan alami dari Kalimantan, tanpa campuran gula, bersertifikat BPOM',
                'price'       => 135000,
                'stock'       => 60,
                'is_active'   => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('✅ Seeder berhasil: 3 kategori dan 10 produk telah dibuat.');
    }
}
