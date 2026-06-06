<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // 追加

class Book extends Model
{
    use HasFactory;

    // 自動採番を使わない設定
    public $incrementing = false;
    // IDの型が文字列であることを明示
    protected $keyType = 'string';

    // 一括代入（fillable）を許可するフィールド
    protected $fillable = ['id', 'book_title', 'book_number', 'category', 'key_word', 'factory_name', 'facility_name', 'file_path'];

    // モデルが作成されるときに自動で UUID を発行する設定
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}