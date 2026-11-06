<?php

namespace Database\Seeders;

use App\Models\{Branch, GarageVehicle, Part, Post, Prospect, PurchaseOrder, ServiceBooking, User, Vehicle, WorkOrder, WorkOrderPart};
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo (kata sandi semuanya: password)
        User::create(['name' => 'Pemilik Dealer', 'email' => 'owner@mejengauto.test', 'password' => 'password', 'role' => 'owner', 'phone' => '081200000001']);
        User::create(['name' => 'Admin Mejeng', 'email' => 'admin@mejengauto.test', 'password' => 'password', 'role' => 'staff', 'phone' => '081200000002']);
        $mekanik1 = User::create(['name' => 'Rudi Mekanik', 'email' => 'rudi@mejengauto.test', 'password' => 'password', 'role' => 'staff', 'phone' => '081200000003']);
        $mekanik2 = User::create(['name' => 'Agus Mekanik', 'email' => 'agus@mejengauto.test', 'password' => 'password', 'role' => 'staff', 'phone' => '081200000004']);
        $budi = User::create(['name' => 'Budi Santoso', 'email' => 'budi@mejengauto.test', 'password' => 'password', 'role' => 'customer', 'phone' => '081234567890']);

        $cabang = collect([
            ['Mejeng Auto Kebayoran', 'Jakarta Selatan', 'Jl. Raya Kebayoran Lama No. 18'],
            ['Mejeng Auto Bekasi', 'Bekasi', 'Jl. Ahmad Yani No. 77'],
            ['Mejeng Auto BSD', 'Tangerang Selatan', 'Jl. BSD Raya Barat No. 5'],
        ])->map(fn ($c) => Branch::create(['name' => $c[0], 'city' => $c[1], 'address' => $c[2]]));

        $mobil = [
            ['Toyota', 'Innova Zenix', 'Hybrid Q', 2025, 489000000, 'MPV', 'Hybrid', 'Putih', 1],
            ['Toyota', 'Fortuner', '2.8 VRZ 4x4', 2024, 679000000, 'SUV', 'Diesel', 'Hitam', 1],
            ['Honda', 'CR-V', '1.5 Turbo Prestige', 2024, 624000000, 'SUV', 'Bensin', 'Silver', 1],
            ['Mitsubishi', 'Xpander', 'Ultimate', 2025, 330000000, 'MPV', 'Bensin', 'Abu-abu', 0],
            ['Suzuki', 'Ertiga', 'Hybrid GX', 2024, 274000000, 'MPV', 'Hybrid', 'Merah', 0],
            ['Hyundai', 'Creta', 'Prime', 2024, 394000000, 'SUV', 'Bensin', 'Biru', 0],
            ['Daihatsu', 'Terios', 'R Deluxe', 2023, 255000000, 'SUV', 'Bensin', 'Putih', 0],
            ['Honda', 'Brio', 'RS CVT', 2024, 215000000, 'Hatchback', 'Bensin', 'Kuning', 0],
        ];
        foreach ($mobil as $m) {
            Vehicle::create([
                'brand' => $m[0], 'model' => $m[1], 'variant' => $m[2], 'year' => $m[3], 'price' => $m[4],
                'body_type' => $m[5], 'fuel' => $m[6], 'color' => $m[7], 'featured' => (bool) $m[8],
                'transmission' => 'Automatic', 'kondisi' => 'baru',
                'description' => "{$m[0]} {$m[1]} {$m[2]} siap kirim. Garansi resmi, free servis berkala pertama, dan opsi kredit fleksibel.",
            ]);
        }

        $parts = collect([
            ['OLI-5W30', 'Oli mesin 5W-30 (1 L)', 40, 12, 98000, 'PT Lubrindo'],
            ['FLT-OLI', 'Filter oli', 25, 10, 65000, 'PT Autoparts Jaya'],
            ['FLT-UDR', 'Filter udara', 14, 8, 120000, 'PT Autoparts Jaya'],
            ['BRK-PAD', 'Kampas rem depan', 9, 6, 380000, 'CV Rem Prima'],
            ['BUSI-IR', 'Busi iridium', 20, 8, 95000, 'PT Autoparts Jaya'],
            ['AC-FREON', 'Freon AC R134a', 6, 6, 150000, 'CV Dingin Sejuk'],
            ['ACCU-NS60', 'Aki NS60', 4, 5, 950000, 'PT Energi Mobil'],
        ])->map(fn ($p) => Part::create(['sku' => $p[0], 'name' => $p[1], 'stock' => $p[2], 'min_stock' => $p[3], 'unit_price' => $p[4], 'supplier' => $p[5]]));
        $parts->each->buatDrafPoJikaMenipis();

        // Garasi pelanggan demo
        $innova = GarageVehicle::create(['user_id' => $budi->id, 'plate' => 'B 1234 MJG', 'name' => 'Toyota Innova Zenix', 'year' => 2024, 'current_km' => 9400, 'avg_km_per_month' => 1200, 'last_service_date' => now()->subMonths(4), 'last_service_km' => 5000]);
        $brio = GarageVehicle::create(['user_id' => $budi->id, 'plate' => 'B 4321 BRO', 'name' => 'Honda Brio RS', 'year' => 2022, 'current_km' => 24500, 'avg_km_per_month' => 900, 'last_service_date' => now()->subMonths(2), 'last_service_km' => 22000]);

        // Riwayat servis (selesai)
        $wo = WorkOrder::create(['code' => 'WO-' . now()->subMonths(4)->format('ymd') . '-001', 'garage_vehicle_id' => $innova->id, 'mechanic_id' => $mekanik1->id, 'status' => 'diambil', 'km_in' => 5000, 'km_out' => 5000, 'labor_cost' => 250000, 'notes' => 'Servis berkala 5.000 km', 'started_at' => now()->subMonths(4), 'finished_at' => now()->subMonths(4)]);
        WorkOrderPart::create(['work_order_id' => $wo->id, 'part_id' => $parts[0]->id, 'qty' => 4, 'price' => $parts[0]->unit_price]);
        WorkOrderPart::create(['work_order_id' => $wo->id, 'part_id' => $parts[1]->id, 'qty' => 1, 'price' => $parts[1]->unit_price]);

        // Booking aktif
        ServiceBooking::create(['user_id' => $budi->id, 'garage_vehicle_id' => $innova->id, 'branch_id' => $cabang[0]->id, 'service_type' => 'Servis berkala', 'booking_date' => now()->addDays(3)->toDateString(), 'booking_time' => '09:00', 'complaint' => 'Servis 10.000 km']);

        // Prospek CRM
        foreach ([['Sari Wulandari', '081311112222', 4, 'baru'], ['Hendra Gunawan', '081322223333', 1, 'dihubungi'], ['Maya Putri', '081333334444', 2, 'survei']] as $i => $p) {
            $v = Vehicle::find($p[2] + $i);
            Prospect::create(['vehicle_id' => $v->id, 'name' => $p[0], 'phone' => $p[1], 'down_payment' => (int) ($v->price * 0.25), 'tenor' => 36, 'installment' => Prospect::hitung($v->price, (int) ($v->price * 0.25), 36), 'status' => $p[3]]);
        }

        Post::create(['title' => 'Langkah baru. Standar lebih tinggi.', 'type' => 'banner', 'body' => 'Gratis servis berkala pertama untuk setiap pembelian unit baru bulan ini.']);
        Post::create(['title' => 'Promo DP ringan hingga 15%', 'type' => 'promo', 'body' => 'Berlaku untuk SUV dan MPV pilihan.']);
        Post::create(['title' => 'Paket ganti oli + cek 21 titik', 'type' => 'promo', 'body' => 'Mulai Rp 350.000 di semua cabang.']);
    }
}
