<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Jika user login
            $table->unsignedBigInteger('package_id'); // Paket yang dipilih
            $table->string('name'); // Nama pemesan
            $table->string('contact'); // Kontak (WA/HP/email)
            $table->dateTime('date'); // Tanggal & jam pemesanan
            $table->string('location'); // Lokasi pemotretan
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->string('status')->default('pending'); // Status pesanan
            $table->string('payment_status')->default('unpaid'); // Status pembayaran
            $table->string('midtrans_order_id')->nullable(); // ID transaksi Midtrans
            $table->decimal('total_price', 12, 2); // Total harga
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
