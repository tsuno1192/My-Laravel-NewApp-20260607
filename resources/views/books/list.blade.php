<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>図書管理台帳 一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">📚 図書管理台帳 一覧</h1>
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center">
          ← ダッシュボードへ戻る
        </a>

           //検索フォーム
           <div class="mb-6">
              <form action="{{ route('books.index') }}" method="GET" class="flex gap-2">
                  <input type="text" name="q" value="{{ request('q') }}" 
                         placeholder="図書名、設備名、キーワードで検索..." 
                         class="flex-grow p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                  <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                      検索
                  </button>
                  <a href="{{ route('books.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                 リセット
                  </a>
              </form>
          </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 text-left text-sm">
                        <th class="p-3 border">ID</th>
                        <th class="p-3 border">工場名</th>
                        <th class="p-3 border">設備名</th>
                        <th class="p-3 border">図書タイトル</th>
                        <th class="p-3 border">図書番号</th>
                        <th class="p-3 border">カテゴリ</th>
                        <th class="p-3 border">キーワード</th>
                        <th class="p-3 border">取扱説明書 (PDF)</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @foreach($books as $book)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 border font-mono">{{ $book->id }}</td>
                        <td class="p-3 border">{{ $book->factory_name }}</td>
                        <td class="p-3 border">{{ $book->facility_name }}</td>
                        <td class="p-3 border font-semibold text-gray-900">{{ $book->book_title }}</td>
                        <td class="p-3 border">{{ $book->book_number }}</td>
                        <td class="p-3 border">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">{{ $book->category }}</span>
                        </td>
                        <td class="p-3 border text-xs text-gray-500">{{ $book->key_word }}</td>
                        <td class="p-3 border">
                            @if($book->file_path && str_starts_with($book->file_path, 'books/'))
                                <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded text-xs inline-block shadow">
                                    📄 PDFを開く
                                </a>
                            @else
                                <span class="text-gray-400">なし</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>