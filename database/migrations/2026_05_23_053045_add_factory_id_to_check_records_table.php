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
            // factory_id を追加し、外部キー制約も設定します
            $table->foreignId('factory_id')->constrained()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('check_records', function (Blueprint $table) {
            $table->dropForeign(['factory_id']);
            $table->dropColumn('factory_id');
        });
    }
};
