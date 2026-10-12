<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('harga_beli', 15, 2)->default(0)->change();
            $table->decimal('harga_jual', 15, 2)->default(0)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('total_harga', 15, 2)->default(0)->change();
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->decimal('harga_satuan', 15, 2)->default(0)->change();
            $table->decimal('subtotal', 15, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->double('harga_beli')->default(0)->change();
            $table->double('harga_jual')->default(0)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->double('total_harga')->default(0)->change();
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->double('harga_satuan')->default(0)->change();
            $table->double('subtotal')->default(0)->change();
        });
    }
};
