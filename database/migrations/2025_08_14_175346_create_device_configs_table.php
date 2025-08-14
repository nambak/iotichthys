<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// device_configs 테이블

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('device_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade')->comment('장치 ID');
            $table->enum('config_type', ['threshold', 'sampling_period', 'setting', 'alert'])->comment('설정 유형');
            $table->string('config_key')->comment('설정 키');
            $table->text('config_value')->comment('설정 값');
            $table->string('unit')->nullable()->comment('단위');
            $table->text('description')->nullable()->comment('설정 설명');
            $table->timestamps();

            // 같은 장치에서 같은 키는 중복될 수 없음
            $table->unique(['device_id', 'config_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_configs');
    }
};
