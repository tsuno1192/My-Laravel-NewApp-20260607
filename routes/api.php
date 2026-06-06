<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PartController;

// 図書用API
Route::post('/books/search', [BookController::class, 'search']);
Route::post('/books', [BookController::class, 'store']);
Route::post('/books/delete', [BookController::class, 'destroy']);

// 部品用API (追加)
Route::post('/parts', [PartController::class, 'store']);
Route::post('/parts/delete', [PartController::class, 'destroy']);