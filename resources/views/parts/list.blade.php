<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>予備品一覧</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">予備品一覧</h1>
        
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">名前</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">型番</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">数量</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($parts as $part)
                    <tr>
                        <td class="px-6 py-4 text-sm">{{ $part[0] ?? '' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $part[1] ?? '' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $part[2] ?? '' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">データがありません</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>