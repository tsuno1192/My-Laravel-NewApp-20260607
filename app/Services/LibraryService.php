<?php

namespace App\Services;

use App\Models\Manual;

class LibraryService
{
    /**
     * キーワードで図書・マニュアルを検索する
     */
    public function search(string $keyword)
    {
        return Manual::where('title', 'LIKE', "%{$keyword}%")
            ->orWhere('doc_number', 'LIKE', "%{$keyword}%")
            ->get();
    }
}