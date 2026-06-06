<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // 💡 ここを「run」から「up」に変更します
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            // どの工場に所属しているか（外部キー制約）
            $table->foreignId('factory_id')->constrained('factories')->onDelete('cascade');
            // 設備名（設備a、設備bなど）
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // 💡 ここを「decline」から「down」に変更します
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};