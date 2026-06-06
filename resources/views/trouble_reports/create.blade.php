<x-app-layout>
    <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
        
        <div class="mb-6 px-4 sm:px-0 flex justify-between items-center">
            <h2 class="text-2xl font-bold leading-7 text-gray-950 flex items-center gap-2">
                <span>📋</span> トラブル報告・補修依頼書の新規発行
            </h2>
            <a href="{{ route('check_records.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                ← 履歴一覧に戻る
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg border border-gray-200">
            <div class="p-6 bg-white border-b border-gray-200">
                
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded text-sm text-red-950">
                    <p class="font-bold text-base mb-1">⚠️ 以下の異常検知に伴う補修依頼です</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700 mt-2">
                        <li><strong>対象設備:</strong> {{ $checkRecord->equipment->name ?? '不明' }}</li>
                        <li><strong>点検日時:</strong> {{ $checkRecord->created_at->format('Y/m/d H:i') }}</li>
                        <li><strong>点検時の備考:</strong> {{ $checkRecord->remarks ?? 'なし' }}</li>
                    </ul>
                </div>

                <form action="{{ route('trouble_reports.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">依頼タイトル</label>
                        <input type="text" name="title" required 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="補修依頼: 設備A (2026-05-24)">
                    </div>

                    <input type="hidden" name="check_record_id" value="{{ $checkRecord->id }}">
                    <input type="hidden" name="equipment_id" value="{{ $checkRecord->equipment->id }}">

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-700 font-bold">① トラブル内容・具体的な状況 <span class="text-red-500">*必須</span></label>
                            <button type="button" data-target="trouble-textarea" class="speech-btn inline-flex items-center gap-1 px-3 py-1 text-sm font-semibold text-blue-600 border border-blue-500 rounded-md hover:bg-blue-50 transition">
                                <span>🎤</span> <span>音声入力を開始</span>
                            </button>
                        </div>
                        <textarea id="trouble-textarea" name="issue_description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="（例）モーター部から異音が発生し..."></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">② 緊急性・優先度 <span class="text-red-500">*必須</span></label>
                        <select name="urgency" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="low">低</option>
                            <option value="medium" selected>中（通常対応・次回定修など）</option>
                            <option value="high">高（緊急対応）</option>
                        </select>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-700 font-bold">③ 推定される原因（わかる範囲で）</label>
                            <button type="button" data-target="cause-textarea" class="speech-btn inline-flex items-center gap-1 px-3 py-1 text-sm font-semibold text-blue-600 border border-blue-500 rounded-md hover:bg-blue-50 transition">
                                <span>🎤</span> <span>音声入力を開始</span>
                            </button>
                        </div>
                        <textarea id="cause-textarea" name="estimated_cause" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="（例）経年劣化によるベアリングの摩耗..."></textarea>
                    </div>

                    <div class="mb-6">
                     <div class="flex items-center justify-between mb-2">
                         <label class="block text-gray-700 font-bold">④ 必要予備品・必要な図書/マニュアル</label>
                         <div class="flex gap-2">
                             <input type="text" id="db-search-input" placeholder="キーワード検索..." class="text-sm border-gray-300 rounded-md">
                             <button type="button" id="search-btn" class="px-3 py-1 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800">検索</button>
            
                        <button type="button" data-target="parts-textarea" class="speech-btn inline-flex items-center gap-1 px-3 py-1 text-sm font-semibold text-blue-600 border border-blue-500 rounded-md hover:bg-blue-50 transition">
                <span>🎤</span> <span>音声入力を開始</span>
            </button>
        </div>
    </div>
    <textarea id="parts-textarea" name="required_parts" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>

    <div id="search-results" class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-md hidden text-sm">
        <h4 class="font-bold mb-2">検索結果:</h4>
        <div id="results-list" class="space-y-2"></div>
    </div>
</div>

                    <hr class="border-gray-200 my-6">

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('check_records.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            キャンセル
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold rounded-md shadow-md transition duration-150 ease-in-out">
                            🚀 補修依頼書を発行する
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div id="global-speech-status" class="mt-2 text-sm text-gray-500 flex items-center gap-2 hidden fixed bottom-4 right-4 bg-white p-3 rounded-lg shadow-lg border border-gray-200 z-50">
        <span class="flex h-3 w-3 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
        <span id="status-text">音声認識中... お話しください。</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const speechStatus = document.getElementById('global-speech-status');

            if (!SpeechRecognition) {
                document.querySelectorAll('.speech-btn').forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                    btn.querySelector('span:last-child').innerText = '音声非対応';
                });
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.lang = 'ja-JP';
            recognition.interimResults = false;
            recognition.continuous = false;

            let isRecognizing = false;
            let activeTargetTextarea = null;
            let activeUserButton = null;

            document.querySelectorAll('.speech-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const targetTextarea = document.getElementById(targetId);

                    if (isRecognizing) {
                        recognition.stop();
                        if (activeUserButton === this) return;
                    }

                    activeTargetTextarea = targetTextarea;
                    activeUserButton = this;
                    recognition.start();
                });
            });

            recognition.onstart = function () {
                isRecognizing = true;
                speechStatus.classList.remove('hidden');
                
                if (activeUserButton) {
                    activeUserButton.classList.replace('text-blue-600', 'text-white');
                    activeUserButton.classList.replace('border-blue-500', 'border-red-500');
                    activeUserButton.classList.add('bg-red-500', 'hover:bg-red-600');
                    activeUserButton.querySelector('span:first-child').innerText = '🛑';
                    activeUserButton.querySelector('span:last-child').innerText = '音声入力を終了';
                }
            };

            recognition.onend = function () {
                isRecognizing = false;
                speechStatus.classList.add('hidden');
                
                document.querySelectorAll('.speech-btn').forEach(btn => {
                    btn.classList.replace('text-white', 'text-blue-600');
                    btn.classList.replace('border-red-500', 'border-blue-500');
                    btn.classList.remove('bg-red-500', 'hover:bg-red-600');
                    btn.querySelector('span:first-child').innerText = '🎤';
                    btn.querySelector('span:last-child').innerText = '音声入力を開始';
                });
            };

            recognition.onresult = function (event) {
                const resultText = event.results[0][0].transcript;
                
                if (activeTargetTextarea) {
                    if (activeTargetTextarea.value.trim() !== "") {
                        activeTargetTextarea.value = activeTargetTextarea.value.trim() + " " + resultText;
                    } else {
                        activeTargetTextarea.value = resultText;
                    }
                    activeTargetTextarea.focus();
                }
            };

            recognition.onerror = function (event) {
                console.error('音声認識エラー:', event.error);
            };
        });
        
        // 検索ボタンの処理
        document.getElementById('search-btn').addEventListener('click', function() {
            const query = document.getElementById('db-search-input').value;
            const resultsArea = document.getElementById('search-results');
            const resultsList = document.getElementById('results-list');

            if (!query) return;

            fetch(`/trouble-reports/search-db?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    resultsList.innerHTML = '';
                    resultsArea.classList.remove('hidden');

                    // 在庫結果を表示
                     data.inventories.forEach(item => {
                        const div = document.createElement('div');
                        div.className = "cursor-pointer p-2 hover:bg-blue-100 border-b";
                        div.innerText = `[在庫] ${item.part_name} (${item.model_number}) - ${item.location}`;
                        div.onclick = () => {
                            document.getElementById('parts-textarea').value += ` ${item.part_name}(${item.model_number})`;
                        };
                        resultsList.appendChild(div);
                    });

                    // 図書結果を表示
                    data.manuals.forEach(item => {
                        const div = document.createElement('div');
                        div.className = "cursor-pointer p-2 hover:bg-green-100 border-b";
                        div.innerText = `[図書] ${item.title} (${item.doc_number})`;
                        div.onclick = () => {
                            document.getElementById('parts-textarea').value += ` ${item.title}`;
                        };
                        resultsList.appendChild(div);
                    });
                });
        });
    </script>
</x-app-layout>