<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6 px-4 sm:px-0">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate flex items-center gap-2">
                    <span>🛠️</span> 点検記録 履歴一覧
                </h2>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    ← ダッシュボードへ戻る
                </a>
            </div>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">点検日時</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">設備名</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">点検結果</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">備考・異状内容/アクション</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($checkRecords as $record)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">
                                            {{ $record->created_at->format('Y/m/d H:i') }}
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-950">{{ $record->equipment->name ?? '不明な設備' }}</div>
                                            <div class="text-xs text-gray-400">（管理番号: {{ $record->equipment->equipment_number ?? '-' }}）</div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($record->status === 'abnormal' || $record->status === '異常あり')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    ⚠️ 異常あり
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    ✅ 異常なし
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            <div class="flex items-center justify-between gap-4">
                                                <span class="max-w-xs truncate">{{ $record->remarks ?? '---' }}</span>
                                                
                                                @if($record->status === '異常あり' || $record->status === '⚠️ 異常あり')
                                                    @if($record->troubleReport)
                                                        <a href="{{ route('trouble_reports.show', $record->troubleReport->id) }}" 
                                                            class="text-blue-600 font-bold hover:underline">
                                                            📋 依頼済み（確認する）
                                                         </a>
                                                    @else
                                                        <a href="{{ route('trouble_reports.create', [
                                                           'equipment_id' => $record->equipment_id,
                                                           'check_record_id' => $record->id]) }}" 
                                                           class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-md shadow-sm transition duration-150 ease-in-out">
                                                            🔧 補修依頼を作成
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">---</span>
                                                @endif
                                            </div>
                                        </td>
                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 whitespace-nowrap text-sm text-gray-500 text-center font-medium">
                                            まだ点検記録がありません。
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>