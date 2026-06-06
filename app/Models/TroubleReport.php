<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TroubleReport extends Model
{
    // 💡 一括保存（マスアサインメント）を許可する項目を指定
    protected $fillable = [
        'check_record_id',
        'equipment_id',
        'user_id',
        'title',
        'description',
        'urgency',
        'estimated_cause',
        'required_parts',
        'status'
    ];

    // 🔗 各データとの繋がり（リレーション）
    public function checkRecord()
    {
        return $this->belongsTo(CheckRecord::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
}
