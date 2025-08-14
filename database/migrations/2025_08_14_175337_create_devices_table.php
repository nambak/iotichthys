<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// devices 테이블

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('장치명');
            $table->string('device_id')->unique()->comment('장치 ID');
            $table->foreignId('device_model_id')->constrained('device_models')->onDelete('cascade')->comment('장치 모델 ID');
            $table->enum('status', ['active', 'inactive', 'maintenance', 'error'])->default('active')->comment('장치 상태');
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade')->comment('소속 조직 ID');
            $table->text('description')->nullable()->comment('장치 설명');
            $table->string('location')->nullable()->comment('장치 위치');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
