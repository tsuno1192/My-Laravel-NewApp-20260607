<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    // IDが文字列（UUID）の場合は、これが必要
    protected $keyType = 'string';
    public $incrementing = false;

    // 書き込みを許可するカラム一覧
    protected $fillable = [
        'id',
        'part_name',
        'quantity',
        'file_path'
    ];
}
