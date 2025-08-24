<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facility_ivas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Kode fasilitas kesehatan');
            $table->string('name')->comment('Nama puskesmas/fasilitas');
            $table->string('location')->comment('Lokasi (Kabupaten/Kota)');
            $table->text('address')->nullable()->comment('Alamat lengkap fasilitas');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Koordinat latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Koordinat longitude');
            $table->string('phone', 20)->nullable()->comment('Nomor telepon');
            $table->json('iva_training_years')->nullable()->comment('Tahun pelatihan IVA yang diikuti');
            $table->boolean('is_active')->default(true)->comment('Status aktif fasilitas');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();

            // Index untuk performa pencarian
            $table->index(['location', 'is_active']);
            $table->index(['latitude', 'longitude']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_ivas');
    }
};