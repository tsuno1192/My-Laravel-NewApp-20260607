<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Factory as ModelFactory;
use App\Models\Inventory;
use App\Models\Manual;


class Equipment extends Model
{
    use HasFactory;

    // テーブル名が自動的に「equipment」にならないよう、明示的に指定します
    protected $table = 'equipments';

    // まとめてデータを保存できるようにする許可設定
    protected $fillable = ['factory_id', 'name'];

    /**
     * この設備が所属している工場を取得する
     */
    public function factory(): BelongsTo
    {
        return $this->belongsTo(ModelFactory::class);
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class);
    }

    public function manuals()
    {
        return $this->belongsToMany(Manual::class);
    }
}
