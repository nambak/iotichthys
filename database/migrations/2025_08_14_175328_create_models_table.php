<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// device_models 테이블 (모델명 충돌 피하기 위해 테이블명 변경)

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('모델명');
            $table->string('manufacturer')->nullable()->comment('제조사');
            $table->json('specifications')->nullable()->comment('모델 사양 (JSON)');
            $table->text('description')->nullable()->comment('모델 설명');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_models');
    }
};
