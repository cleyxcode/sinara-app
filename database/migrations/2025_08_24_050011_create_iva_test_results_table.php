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
        Schema::create('iva_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user_apps')->onDelete('cascade');
            $table->date('examination_date');
            $table->string('examination_type');
            $table->enum('result', ['positif', 'negatif']);
            $table->text('notes')->nullable();
            $table->string('examined_by')->nullable(); // Nama pemeriksa/dokter
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'examination_date']);
            $table->index('result');
            $table->index('examination_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iva_test_results');
    }
};