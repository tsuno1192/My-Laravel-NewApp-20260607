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
        Schema::table('check_records', function (Blueprint $table) {
            // 💡 点検結果を保存する status カラムを新しく追加
        $table->string('status')->default('異常なし')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('check_records', function (Blueprint $table) {
            // 💡 追加した status カラムを削除する（元に戻す）
            $table->dropColumn('status');
        });
    }
};
