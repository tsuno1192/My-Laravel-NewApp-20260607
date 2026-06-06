<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckRecord;
use App\Models\TroubleReport;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Manual;
use App\Models\Equipment;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use Spatie\Browsershot\Browsershot;
use App\Services\InventoryService;
use App\Services\LibraryService;


class TroubleReportController extends Controller
{
    protected InventoryService $inventoryService;
    protected LibraryService $libraryService;

    public function __construct(InventoryService $inventoryService, LibraryService $libraryService)
    {
    $this->inventoryService = $inventoryService;
    $this->libraryService = $libraryService;
    }
   
    /**
     * 補修依頼 作成画面の表示
     */
    public function create(Request $request)
    {
        // 💡 どの点検記録から来たか、IDを取得
        $checkRecordId = $request->query('check_record_id');
        
        // その点検記録と、紐づく設備データを一緒に取得する（なければ404エラー）
        $checkRecord = CheckRecord::with('equipment')->findOrFail($checkRecordId);

        // 設備情報を取得
         $equipment = Equipment::findOrFail($checkRecord->equipment_id);

        // この設備に関連するパーツとマニュアルをまとめて取得
        $inventories = $checkRecord->equipment->inventories;
        $manuals = $checkRecord->equipment->manuals;

        // 作成画面（ビュー）にデータを渡して表示
        return view('trouble_reports.create', compact('checkRecord', 'inventories', 'manuals'));
    }

    /**
     * 補修依頼の保存処理
     */
    public function store(Request $request)
    {
        // 入力値のチェック（画面の項目名 issue_description に合わせました）
        $validated = $request->validate([
            'check_record_id' => 'required|exists:check_records,id',
            'equipment_id'   => 'required|exists:equipments,id',
            'title' => 'required|string|max:255',
            'issue_description' => 'required|string|max:1000', // 変更点
            'urgency'        => 'required|string',
            'estimated_cause'=> 'nullable|string|max:1000',
            'required_parts' => 'nullable|string|max:1000',
        ]);

        // 保存処理
        TroubleReport::create([
            'check_record_id' => $validated['check_record_id'],
            'equipment_id'   => $validated['equipment_id'],
            'user_id'        => Auth::id(),
            'title'          => $validated['title'],
            'description'    => $validated['issue_description'], // 画面からの値を入れる
            'urgency'        => $validated['urgency'],
            'estimated_cause'=> $validated['estimated_cause'],
            'required_parts' => $validated['required_parts'],
            'status'         => '未対応',
        ]);

        // 保存したら、元の点検記録一覧画面に戻る（サクセスメッセージ付き）
        return redirect()->route('check_records.index')->with('status', '補修依頼書を正常に発行しました！');
    }

    // TroubleReportController のクラス内に追記してください

     public function searchDb(Request $request)
    {
         $keyword = $request->input('query');

         if (!$keyword) {
             return response()->json(['inventories' => [], 'manuals' => []]);
         }

         // 在庫と図書をキーワードで検索
         $inventories = $this->inventoryService->search($keyword);
         $manuals= $this->libraryService->search($keyword);                      
        
         return response()->json([
             'inventories' => $inventories,
             'manuals' => $manuals
         ]);

    }

    public function show($id)
    {
    // 必要なデータを取得してビューに渡すだけ
    $report = TroubleReport::with('checkRecord.equipment')->findOrFail($id);
    
    return view('trouble_reports.show', compact('report'));
   }

}