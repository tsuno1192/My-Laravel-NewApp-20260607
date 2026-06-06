<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Services\GoogleSheetsService;

class PartController extends Controller
{
    /**
     * @var \App\Services\GoogleSheetsService
     */
    protected $sheets;

    public function __construct(GoogleSheetsService $sheets)
    {
        $this->sheets = $sheets;
    }

    /**
     * 部品用：Google Sheets一覧取得（新規シート用）
     */
    public function indexSheet()
    {
        // ★ここに新しい部品管理用シートのIDを設定してください
        $spreadsheetId = '1qMs9WFtvD_a_6w7NNZUMxaOm6gmI0VybZ-l1SvszwB0';
        $range = 'シート1!A1:D10';

        $parts = $this->sheets->readSheet($spreadsheetId, $range);
        return view('parts.index', compact('parts'));
    }

    /**
     * AppSheetからの部品登録・更新 (Webhook用)
     */
    public function store(Request $request)
    {
        // データの受け取り
        $rawContent = $request->getContent();
        $outerData = json_decode($rawContent, true);
        $contentString = $outerData['content'] ?? $rawContent;
        $partData = json_decode($contentString, true) ?? [];

        // 1. 必須項目チェック
        if (empty($partData['id']) || empty($partData['part_name']) || empty($partData['factory_name'])) {
            Log::warning('必須項目不足:', $partData);
            return response()->json(['status' => 'error', 'message' => '必須項目不足'], 422);
        }

        // 2. 時刻補正
        $partData['created_at'] = now();

        // 3. ファイル処理（PDF）
        // return を挟まずに処理を完了させます
        if (!empty($partData['file_path']) && filter_var($partData['file_path'], FILTER_VALIDATE_URL)) {
            $url = $partData['file_path'];
            $fileName = 'parts/' . $partData['id'] . '_' . Str::random(6) . '.pdf';

            $response = Http::get($url);
            if ($response->successful()) {
                Storage::disk('public')->put($fileName, $response->body());
                $partData['file_path'] = $fileName;
            }
        }

        // 4. 保存（UPSERT）
        $part = Part::updateOrCreate(['id' => $partData['id']], $partData);

        Log::info('部品Webhookを正常に処理しました:', ['id' => $partData['id']]);

        // 5. 最後に一回だけリターンする
        return response()->json(['status' => 'success', 'data' => $part], 201);
    }

    /**
     * 部品削除処理
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $part = Part::where('id', $id)->first();

        if ($part) {
            if ($part->file_path) Storage::disk('public')->delete($part->file_path);
            $part->delete();
            return response()->json(['message' => '部品を削除しました'], 200);
        }
        return response()->json(['message' => '対象が見つかりません'], 404);
    }
}
