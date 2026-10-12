<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_anggota')->unique();
            $table->string('nama');
            $table->string('telepon', 30)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_bergabung');
            $table->timestamps();
        });

        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->string('jenis', 20);
            $table->decimal('jumlah', 15, 2);
            $table->string('petugas');
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal');
            $table->timestamps();
            $table->index(['member_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_transactions');
        Schema::dropIfExists('members');
    }
};
