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
        Schema::create('user_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user_apps')->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->json('responses'); 
            $table->integer('total_score')->default(0);
            $table->string('risk_level')->nullable(); 
            $table->text('recommendations')->nullable(); 
            $table->timestamp('completed_at');
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['user_id', 'completed_at']);
            $table->index('risk_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_responses');
    }
};