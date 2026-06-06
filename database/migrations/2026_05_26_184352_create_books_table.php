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
        Schema::create('books', function (Blueprint $table) {
            // SQLの主キー（AppSheetのUNIQUEIDを受け止めるため、文字列型のuuidまたはstringにする）
        $table->string('id')->primary(); 
        
        $table->string('factory_name');   // 工場名
        $table->string('facility_name');  // 設備名
        $table->string('book_title');     // 図書名
        $table->string('book_number')->nullable(); // 図書番号（空でもOK）
        $table->string('category');       // カテゴリ
        $table->text('key_word')->nullable();      // 検索キーワード（長文対応でtext型）
        $table->string('file_path');      // ファイルの相対パス
        
        $table->timestamps(); // created_at, updated_at を自動生成
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
