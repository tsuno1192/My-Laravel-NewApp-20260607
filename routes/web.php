<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CheckRecordController;
use App\Http\Controllers\TroubleReportController;
use Illuminate\Support\Facades\Route;
use App\Models\Equipment;
use App\Models\CheckRecord;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PartController;

// --- 公開用ルート ---
Route::get('/', function () {
    return view('welcome');
});

// 予備品管理
Route::get('/parts', [PartController::class, 'index'])->name('parts.index');

// Google Sheets からのデータ表示用
Route::get('/books/sheets', [BookController::class, 'indexSheet']);

// --- 認証が必要なルート ---
Route::middleware('auth')->group(function () {
    
    // ダッシュボード
    Route::get('/dashboard', function () {
        $user = Auth::user();
        $factoryId = $user->factory_id;
        
        $equipments = $factoryId 
            ? Equipment::where('factory_id', $factoryId)->get() 
            : collect();
            
        $checkRecords = $factoryId 
            ? CheckRecord::where('factory_id', $factoryId)->latest()->take(10)->get() 
            : collect();

        return view('dashboard', compact('equipments', 'checkRecords'));
    })->name('dashboard');

    // プロフィール関連
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 点検系ルート
    Route::get('/equipment/{equipment_id}/check', [CheckRecordController::class, 'create'])->name('check_records.create');
    Route::post('/equipment/{equipment_id}/check', [CheckRecordController::class, 'store'])->name('check_records.store');
    Route::get('/check-records', [CheckRecordController::class, 'index'])->name('check_records.index');

    // 補修依頼系ルート
    Route::get('/equipment/{equipment_id}/trouble-report/create', [TroubleReportController::class, 'create'])->name('trouble_reports.create');
    Route::post('/trouble-reports', [TroubleReportController::class, 'store'])->name('trouble_reports.store');
    Route::get('/trouble-reports/search', [TroubleReportController::class, 'searchDb'])->name('trouble-reports.search');
    Route::get('/trouble-reports/{id}/pdf', [TroubleReportController::class, 'downloadPdf'])->name('trouble_reports.pdf');
    Route::get('/trouble-reports/{id}', [TroubleReportController::class, 'show'])->name('trouble_reports.show');

    // 図書管理一覧画面（データベース用）
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
});

require __DIR__.'/auth.php';