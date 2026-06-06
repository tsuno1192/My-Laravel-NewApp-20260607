<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Services\GoogleSheetsService;

class BookController extends Controller
{

     protected $sheets;

        public function __construct(GoogleSheetsService $sheets)
        {
           $this->sheets = $sheets;
        }


        /**
         * ブラウザ用一覧表示（Google Sheets連携）
        */
        public function indexSheet()
    {
        // 注意：URL全体ではなくID部分のみを使用します
        $spreadsheetId = '1cS2KhjNHg9Yd3S1IVD8mHQcAICUQjdIGUW_ypvuzUic';
        $range = 'シート1!A1:D10';

        // サービス経由でデータを取得
        $books = $this->sheets->readSheet($spreadsheetId, $range);

        // ビューに渡す
        return view('books.index', compact('books'));
    }

    /**
     * AppSheetからの登録・更新受付（Webhook用API）
     */
    public function store(Request $request)
    {
        // 元々完璧に動いていた「二重JSONを紐解く処理」をそのまま100%維持します
        $rawContent = $request->getContent();
        $outerData = json_decode($rawContent, true);
        $contentString = $outerData['content'] ?? $rawContent;
        $bookData = json_decode($contentString, true) ?? [];

        // 🚀 ここに追記！届いたリクエスト内容をそのままログに出力
        Log::info('AppSheetからのWebhookを受信しました:', $request->all());

        $rawContent = $request->getContent();

        // バリデーション
        if (empty($bookData['id']) || empty($bookData['book_title']) || empty($bookData['factory_name'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'バリデーション不合格: id, factory_name, book_title は必須項目です。'
            ], 422);
        }

        // 添付ファイル（PDF）の本体ダウンロード処理
        if (!empty($bookData['file_path']) && filter_var($bookData['file_path'], FILTER_VALIDATE_URL)) {
            try {
                $url = $bookData['file_path'];
                $fileName = 'books/' . $bookData['id'] . '_' . Str::random(6) . '.pdf';

                $response = Http::timeout(30)->get($url);

                if ($response->successful()) {
                    Storage::disk('public')->put($fileName, $response->body());
                    $bookData['file_path'] = $fileName;
                    Log::info('PDFダウンロード成功:', ['path' => $fileName]);
                } else {
                    Log::warning('PDF取得失敗（ステータスエラー）:', ['status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error('PDFダウンロード中に例外発生: ' . $e->getMessage());
            }
        }

        // データベースに保存（UPSERT）
        try {
            $book = Book::updateOrCreate(
                ['id' => $bookData['id']], 
                $bookData
            );

            $wasRecentlyCreated = $book->wasRecentlyCreated ? '新規登録' : '上書き更新';
            Log::info("図書データを{$wasRecentlyCreated}しました: ID " . $book->id);

            return response()->json([
                'status' => 'success',
                'message' => "図書データが正常に{$wasRecentlyCreated}されました。",
                'data' => $book
            ], 201);

        } catch (\Exception $e) {
            Log::error('DB保存エラー: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'DB保存に失敗しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * WEBブラウザ用に図書一覧画面を表示するメソッド
     */
    // Requestを忘れずにインポートしてください (use Illuminate\Http\Request;)
    public function index(Request $request) 
    {
     // 入力された検索キーワード（'q'）を取得
     $query = $request->input('q');

       // 検索がある場合はフィルタリングし、ない場合は全件取得する
        $books = Book::when($query, function ($q) use ($query) {
         return $q->where('book_title', 'like', "%{$query}%")
                  ->orWhere('facility_name', 'like', "%{$query}%")
                  ->orWhere('key_word', 'like', "%{$query}%")
                  ->orWhere('book_number', 'like', "%{$query}%"); // 図書番号も対象に追加しました
        })
        ->orderBy('created_at', 'desc')
        ->get();

     return view('books.list', compact('books'));
    }

    /**
     * AppSheetからの削除受付（Webhook用API）
     */
    public function destroy(Request $request)
    {
        // 削除時も、AppSheetから届く生のデータをそのまま取得します
        $rawContent = $request->getContent();
        $outerData = json_decode($rawContent, true);
        
        // データの届き方に柔軟に対応できるようにします
        $contentString = $outerData['content'] ?? null;
        if ($contentString) {
            $innerData = json_decode($contentString, true);
            $id = $innerData['id'] ?? null;
        } else {
            $id = $outerData['id'] ?? $request->input('id');
        }

        if (!$id) {
            Log::warning('削除Webhook受信: IDが空のため処理をスキップしました。');
            return response()->json(['status' => 'error', 'message' => 'IDが指定されていません。'], 422);
        }

        try {
            // 💡【ここが唯一の修正点】
            // find($id)ではなく、文字列のID（90c55b45など）でも確実に一致するデータをDBから探します
            $book = Book::where('id', $id)->first();

            if ($book) {
                // PDFファイルがあれば削除
                if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                    Storage::disk('public')->delete($book->file_path);
                    Log::info('図書削除に伴い、PDFファイルも削除しました:', ['path' => $book->file_path]);
                }

                // データベースから削除
                $book->delete();
                Log::info("図書データを削除しました: ID {$id}");

                return response()->json([
                    'status' => 'success',
                    'message' => "ID {$id} の図書データと添付ファイルを削除しました。"
                ], 200);
            }

            Log::info("削除対象のID {$id} はデータベースに存在しませんでした。");
            return response()->json(['status' => 'success', 'message' => 'すでに対象データはありません。'], 200);

        } catch (\Exception $e) {
            Log::error('削除処理中にエラー発生: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => '削除に失敗しました。'], 500);
        }
    }

    /**
     * AppSheetからの検索リクエストを受け取り、該当図書を返す
     */
    public function search(Request $request)
    {
        // AppSheetから届くキーワードを取得
        $keyword = $request->input('keyword');

        // キーワードが空の場合は全件取得、または空の配列を返す
        if (empty($keyword)) {
            return response()->json(['message' => 'キーワードを入力してください'], 400);
        }

        // データベースから検索（図書名、キーワード、設備名で検索）
        $books = Book::where('book_title', 'like', "%{$keyword}%")
                     ->orWhere('key_word', 'like', "%{$keyword}%")
                     ->orWhere('facility_name', 'like', "%{$keyword}%")
                     ->get();

        return response()->json($books);
    }
    
}