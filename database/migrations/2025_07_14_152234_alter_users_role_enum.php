<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ubah kolom role menjadi ENUM
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke varchar jika di-rollback
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->change();
        });
    }
};
