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
        Schema::create('trouble_reports', function (Blueprint $table) {
            $table->id();

            // 💡 どの「点検記録」から紐づいたトラブルなのかを記録
            $table->foreignId('check_record_id')->constrained()->onDelete('cascade');

            // 💡 どの「設備」のトラブルなのか（点検記録からも辿れますが、あると便利です）
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
           
            // 💡 誰がこの依頼を出したか（ログインユーザー）
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 📋 トラブル・補修依頼の管理項目
            $table->string('title'); // トラブルのタイトル
            $table->text('description'); // 詳細な説明
            $table->string('urgency')->default('中'); // 緊急度（例: 高、中、低）
            $table->string('estimated_cause')->nullable(); // 推定される原因
            $table->text('required_parts')->nullable();   // 必要予備品/図書（空欄でもOK）
            $table->string('status')->default('未対応');   // 対応ステータス（未対応、対応中、完了など）

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trouble_reports');
    }
};
