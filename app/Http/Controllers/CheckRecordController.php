<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CheckRecord;
use App\Models\Equipment;
use Illuminate\Support\Facades\Auth;
use App\Mail\DailyCheckReport;
use Illuminate\Support\Facades\Mail;    
use Illuminate\Support\Facades\Log;

class CheckRecordController extends Controller
{
     /**
     * 点検入力フォーム画面を表示する
     */
    public function create(int $equipment_id)
    {
        $equipment = Equipment::findOrFail($equipment_id);
        return view('check_records.create', compact('equipment'));
    }

    /**
     * 入力された点検データをデータベースに保存する
     */
    public function store(Request $request, int $equipment_id)
    {
        // 1. 入力チェック（バリデーション）を実行
        $validatedData = $request->validate([
            'item_1' => 'required|string',
            'item_2' => 'required|string',
            'item_3' => 'required|string',
            'item_4' => 'required|string',
            'item_5' => 'required|string',
            'item_6' => 'required|string',
            'item_7' => 'required|string',
            'item_8' => 'required|string',
            'item_9' => 'required|string',
            'item_10' => 'required|string',
            'notes' => 'nullable|string',
            'next_replenishment_date' => 'nullable|date'
        ]);

        // 💡 【ここに追加しました！】 10個の項目の中に1つでも「異常あり」があるか自動判定
        $hasAbnormal = false;
        for ($i = 1; $i <= 10; $i++) {
           // 画面側の name="item_1" 〜 "item_10" に完全対応させます
           $itemValue = $validatedData["item_{$i}"] ?? null;
           if ($itemValue === '異常あり') {
               $hasAbnormal = true;
               break;
            }
        }
        
        // 判定結果を文字としてセット
        $status = $hasAbnormal ? '異常あり' : '異常なし';

        // 2. 新しい点検記録を作成してデータベースに保存
        CheckRecord::create([
            'factory_id' => Auth::user()->factory_id, // ログインユーザーの工場IDを保存
            'equipment_id' => $equipment_id,
            'user_id' => Auth::id(), // ログインユーザーのIDを保存
            'status' => $status,    // 💡 【ここに追加！】自動判定されたステータスを保存
            'item_1' => $validatedData['item_1'],
            'item_2' => $validatedData['item_2'],
            'item_3' => $validatedData['item_3'],
            'item_4' => $validatedData['item_4'],
            'item_5' => $validatedData['item_5'],
            'item_6' => $validatedData['item_6'],
            'item_7' => $validatedData['item_7'],
            'item_8' => $validatedData['item_8'],
            'item_9' => $validatedData['item_9'],
            'item_10' => $validatedData['item_10'],
            'notes' => $validatedData['notes'] ?? null,
            'next_replenishment_date' => $validatedData['next_replenishment_date'] ?? null
        ]);

        // 3. メール送信処理
        try {
            // メールクラスに渡すために、設備情報をDBから取得します
            $equipment = Equipment::findOrFail($equipment_id);

            // 送信先メールアドレス（テスト用）
            $adminEmail = 'leader@example.com';

            // メール送信実行
            Mail::to($adminEmail)->send(new DailyCheckReport($equipment, $request->all()));
        } catch (\Exception $e) {
            // メール送信エラーでシステム全体が落ちないようにログに記録する
            Log::error('メール送信エラー: ' . $e->getMessage());
        }

        // 4. 保存後はダッシュボード画面に戻る
        return redirect()->route('dashboard')->with('status', '点検記録を保存し、管理者にメール通知しました。');
    }

    /**
     * 点検記録の一覧を表示する
     */
    public function index()
    {
        // 💡 記録を新しい順（latest）で取得し、関係する「設備（equipment）」の情報も一緒に持ってきます
        // 💡 さらに今回作った「トラブル報告（troubleReport）」の情報も一緒に持ってきてボタンの出し分けに備えます
        $checkRecords = CheckRecord::with(['equipment', 'troubleReport'])->latest()->get();
        
        // 一覧画面のビューにデータを渡して表示します
        return view('check_records.index', compact('checkRecords'));
    }
}