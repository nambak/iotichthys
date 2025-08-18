<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// organizations 테이블

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('사업자명');
            $table->string('owner')->comment('대표자');
            $table->string('business_register_number')->unique()->comment('사업자 번호');
            $table->string('postcode', 5)->nullable()->after('address')->comment('우편번호');
            $table->string('address')->comment('사업장 주소');
            $table->string('detail_address')->nullable()->after('postcode')->comment('상세주소');
            $table->string('phone_number')->comment('사업자 대표전화');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
