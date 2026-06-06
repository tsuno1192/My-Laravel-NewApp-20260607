<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TroubleReport;

class CheckRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'factory_id', // 💡 追加：工場IDも保存するようにします
        'equipment_id',
        'user_id',
        'status',
        'item_1',
        'item_2',
        'item_3',
        'item_4',
        'item_5',
        'item_6',
        'item_7',
        'item_8',
        'item_9',
        'item_10',
        'notes',
        'next_replenishment_date'
    ];

    /**
     * 💡 追加：この点検記録が「どの設備」のものかを取得する
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * 💡 追加：この点検記録を「誰が」書いたかを取得する
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
 * トラブル報告（補修依頼）とのリレーション
 */
    public function troubleReport()
    {
        return $this->hasOne(TroubleReport::class);
    }
}

