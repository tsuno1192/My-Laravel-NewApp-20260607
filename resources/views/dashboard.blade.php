<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="flex justify-start px-4 sm:px-0">
                <a href="{{ route('check_records.index') }}" class="inline-flex items-center px-5 py-3 bg-slate-800 hover:bg-slate-900 text-white rounded-md font-bold text-sm shadow-md gap-2 transition duration-150">
                    <span class="text-base">🛠️</span>
                    <span class="text-white">点検記録 履歴一覧（全件）を見る</span>
             </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('status'))
                        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-bold rounded shadow-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-bold mb-4">■ あなたの工場の管理設備一覧</h3>

                    @if($equipments->isEmpty())
                        <p class="text-red-500">登録されている設備がありません。</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($equipments as $equipment)
                                <div class="p-4 bg-gray-100 rounded-lg border border-gray-200 shadow-sm flex justify-between items-center">
                                    <span class="text-xl font-semibold text-gray-700">{{ $equipment->name }}</span>
                                    <a href="{{ route('check_records.create', $equipment->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-bold shadow">
                                        点検開始
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">📋 本日・過去の点検履歴（最新10件）</h3>

                    @if($checkRecords->isEmpty())
                        <p class="text-gray-500">まだ点検履歴がありません。最初の点検を行ってください。</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-800 text-white font-bold">
                                        <th class="p-3 rounded-l">点検日時</th>
                                        <th class="p-3">対象設備</th>
                                        <th class="p-3">点検者</th>
                                        <th class="p-3">項目1〜5</th>
                                        <th class="p-3">項目6〜10</th>
                                        <th class="p-3 rounded-r">備考・次補充</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($checkRecords as $record)
                                        <tr class="hover:bg-gray-50">
                                            <td class="p-3 text-sm text-gray-600 font-medium">
                                                {{ $record->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="p-3 font-bold text-gray-800">
                                                {{ $record->equipment->name }}
                                            </td>
                                            <td class="p-3 text-sm text-gray-700">
                                                {{ $record->user->name }}
                                            </td>
                                            <td class="p-3 text-xs space-y-0.5">
                                                <div>1.振動: <span class="{{ $record->item_1 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_1 }}</span></div>
                                                <div>2.音臭: <span class="{{ $record->item_2 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_2 }}</span></div>
                                                <div>3.温度: <span class="{{ $record->item_3 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_3 }}</span></div>
                                                <div>4.電圧: <span class="{{ $record->item_4 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_4 }}</span></div>
                                                <div>5.電流: <span class="{{ $record->item_5 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_5 }}</span></div>
                                            </td>
                                            <td class="p-3 text-xs space-y-0.5">
                                                <div>6.プロ: <span class="{{ $record->item_6 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_6 }}</span></div>
                                                <div>7.油量: <span class="{{ $record->item_7 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_7 }}</span></div>
                                                <div>8.油漏: <span class="{{ $record->item_8 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_8 }}</span></div>
                                                <div>9.薬量: <span class="{{ $record->item_9 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_9 }}</span></div>
                                                <div>10.薬漏: <span class="{{ $record->item_10 == '異常あり' ? 'text-red-600 font-bold' : 'text-green-600' }}">{{ $record->item_10 }}</span></div>
                                            </td>
                                            <td class="p-3 text-xs text-gray-600 max-w-xs truncate">
                                                @if($record->notes) <div class="italic mb-1">"{{ $record->notes }}"</div> @endif
                                                @if($record->next_replenishment_date) <div class="text-blue-600 font-semibold">📅 補充: {{ $record->next_replenishment_date }}</div> @endif

                                                {{-- 「異常あり」の場合のみ補修依頼ボタンを表示 --}}
                                                @if($record->status === '異常あり')
                                                    <a href="{{ route('trouble_reports.create', [
                                                    'equipment_id' => $record->equipment_id,
                                                    'check_record_id' => $record->id]) }}"
                                                       class="inline-block bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded font-bold text-xs shadow transition">
                                                        ⚠️ 補修依頼する
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
