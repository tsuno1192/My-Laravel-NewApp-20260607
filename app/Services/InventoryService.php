<?php

namespace App\Services;

use App\Models\Inventory;

class InventoryService
{
    /**
     * キーワードで予備品を検索する
     */
    public function search(string $keyword)
    {
        return Inventory::where('part_name', 'LIKE', "%{$keyword}%")
            ->orWhere('model_number', 'LIKE', "%{$keyword}%")
            ->get();
    }
}