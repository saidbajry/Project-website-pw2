<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('role', 20)->default('customer')->after('password');
            $t->string('phone', 30)->nullable()->after('role');
        });

        Schema::create('branches', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('city');
            $t->string('address')->nullable();
            $t->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $t) {
            $t->id();
            $t->string('brand');
            $t->string('model');
            $t->string('variant')->nullable();
            $t->unsignedSmallInteger('year');
            $t->unsignedBigInteger('price');
            $t->string('transmission', 20)->default('Automatic');
            $t->string('fuel', 20)->default('Bensin');
            $t->string('body_type', 30);
            $t->string('color', 30)->nullable();
            $t->unsignedInteger('mileage')->default(0);
            $t->string('kondisi', 10)->default('baru');
            $t->string('status', 20)->default('tersedia');
            $t->string('image_url', 500)->nullable();
            $t->text('description')->nullable();
            $t->boolean('featured')->default(false);
            $t->timestamps();
        });

        Schema::create('prospects', function (Blueprint $t) {
            $t->id();
            $t->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('phone', 30);
            $t->string('email')->nullable();
            $t->unsignedBigInteger('down_payment')->default(0);
            $t->unsignedTinyInteger('tenor')->default(36);
            $t->unsignedBigInteger('installment')->default(0);
            $t->string('status', 20)->default('baru');
            $t->text('notes')->nullable();
            $t->timestamps();
        });

        Schema::create('garage_vehicles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('plate', 20)->unique();
            $t->string('name');
            $t->unsignedSmallInteger('year')->nullable();
            $t->unsignedInteger('current_km')->default(0);
            $t->unsignedInteger('avg_km_per_month')->default(1000);
            $t->date('last_service_date')->nullable();
            $t->unsignedInteger('last_service_km')->default(0);
            $t->unsignedInteger('interval_km')->default(5000);
            $t->unsignedTinyInteger('interval_months')->default(6);
            $t->timestamps();
        });

        Schema::create('service_bookings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('garage_vehicle_id')->constrained()->cascadeOnDelete();
            $t->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $t->string('service_type');
            $t->date('booking_date');
            $t->string('booking_time', 5);
            $t->text('complaint')->nullable();
            $t->string('status', 20)->default('menunggu');
            $t->timestamps();
        });

        Schema::create('work_orders', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->foreignId('service_booking_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('garage_vehicle_id')->constrained()->cascadeOnDelete();
            $t->foreignId('mechanic_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('status', 20)->default('antri');
            $t->unsignedInteger('km_in')->default(0);
            $t->unsignedInteger('km_out')->nullable();
            $t->unsignedBigInteger('labor_cost')->default(0);
            $t->text('notes')->nullable();
            $t->timestamp('started_at')->nullable();
            $t->timestamp('finished_at')->nullable();
            $t->timestamps();
        });

        Schema::create('parts', function (Blueprint $t) {
            $t->id();
            $t->string('sku')->unique();
            $t->string('name');
            $t->integer('stock')->default(0);
            $t->integer('min_stock')->default(5);
            $t->unsignedBigInteger('unit_price')->default(0);
            $t->string('supplier')->nullable();
            $t->timestamps();
        });

        Schema::create('work_order_parts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('part_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('qty');
            $t->unsignedBigInteger('price');
            $t->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('part_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('qty');
            $t->string('status', 20)->default('draf');
            $t->timestamps();
        });

        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('type', 20)->default('promo');
            $t->text('body')->nullable();
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });

        Schema::create('reminder_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('garage_vehicle_id')->constrained()->cascadeOnDelete();
            $t->string('phone', 30);
            $t->text('message');
            $t->string('status', 20)->default('simulasi');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['reminder_logs', 'posts', 'purchase_orders', 'work_order_parts', 'parts', 'work_orders',
                  'service_bookings', 'garage_vehicles', 'prospects', 'vehicles', 'branches'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['role', 'phone']));
    }
};
