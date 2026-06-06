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
        Schema::create('check_records', function (Blueprint $table) {
            $table->id();
            // どの設備を点検したか（equipmentsテーブルと紐付け、設備が消えたら記録も消す設定）
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            // 誰が点検したか（usersテーブルと紐付け）
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // --- 計画書の10の点検項目 ---
            $table->string('item_1')->comment('機器振動');
            $table->string('item_2')->comment('異音/異臭');
            $table->string('item_3')->comment('状態温度');
            $table->string('item_4')->comment('電圧/周波数');
            $table->string('item_5')->comment('電流/抵抗値');
            $table->string('item_6')->comment('プロセス値');
            $table->string('item_7')->comment('設備のグリスや潤滑油などの残量');
            $table->string('item_8')->comment('潤滑油などの漏洩の有無');
            $table->string('item_9')->comment('薬品の残量');
            $table->string('item_10')->comment('薬品などの漏洩');

            // --- 入力支援（音声入力用と次回補充日） ---
            $table->text('notes')->nullable()->comment('備考欄（音声入力テキスト用）');
            $table->date('next_replenishment_date')->nullable()->comment('次回の油脂・薬品の補充日');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_records');
    }
};
