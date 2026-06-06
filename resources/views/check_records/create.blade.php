<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $equipment->name }} - 日常点検入力
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('check_records.store', $equipment->id) }}" method="POST">
                    @csrf

                    <h3 class="text-lg font-bold mb-6 text-blue-600">■ 点検項目（10項目）</h3>

                    @php
                        $items = [
                            'item_1' => '機器振動',
                            'item_2' => '異音/異臭',
                            'item_3' => '状態温度',
                            'item_4' => '電圧/周波数',
                            'item_5' => '電流/抵抗値',
                            'item_6' => 'プロセス値',
                            'item_7' => '設備のグリスや潤滑油などの残量',
                            'item_8' => '潤滑油などの漏洩の有無',
                            'item_9' => '薬品の残量',
                            'item_10' => '薬品などの漏洩',
                        ];
                    @endphp

                    <div class="space-y-6">
                        @foreach($items as $key => $label)
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <span class="font-medium text-gray-800">{{ $loop->iteration }}. {{ $label }}</span>
                                <div class="flex gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="{{ $key }}" value="異常なし" checked class="w-5 h-5 text-green-600">
                                        <span class="ml-2 font-bold text-green-700">〇 異常なし</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="{{ $key }}" value="異常あり" class="w-5 h-5 text-red-600">
                                        <span class="ml-2 font-bold text-red-700">✕ 異常あり</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-8">

                    <!-- 💡 備考欄（音声入力ボタン付き） -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-gray-700 font-bold">💡 備考（音声入力など）</label>
                            
                            <!-- 音声入力スタートボタン (Tailwindで綺麗に装飾) -->
                            <button type="button" id="start-speech-btn" class="inline-flex items-center gap-1 px-3 py-1 text-sm font-semibold text-blue-600 border border-blue-500 rounded-md hover:bg-blue-50 transition duration-150 ease-in-out">
                                <span id="mic-icon">🎤</span>
                                <span id="btn-text">音声入力を開始</span>
                            </button>
                        </div>
                        
                        <!-- テキストエリア (既存のname="notes"を維持) -->
                        <textarea id="remarks-textarea" name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="特記事項があれば入力してください（音声入力も可能です）"></textarea>
                        
                        <!-- 状態表示用（音声認識中のステータス） -->
                        <div id="speech-status" class="mt-2 text-sm text-gray-500 flex items-center gap-2 hidden">
                            <span class="flex h-3 w-3 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                            </span>
                            <span>音声認識中... お話しください。</span>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-gray-700 font-bold mb-2">📅 次回の油脂・薬品 補充予定日</label>
                        <input type="date" name="next_replenishment_date" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-bold shadow">
                            戻る
                        </a>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-bold shadow text-lg">
                            点検結果を送信・保存
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- 🛠️ JavaScript（Web Speech APIの制御） -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            
            const startBtn = document.getElementById('start-speech-btn');
            const remarksTextarea = document.getElementById('remarks-textarea');
            const speechStatus = document.getElementById('speech-status');
            const micIcon = document.getElementById('mic-icon');
            const btnText = document.getElementById('btn-text');

            if (!SpeechRecognition) {
                startBtn.disabled = true;
                startBtn.classList.add('opacity-50', 'cursor-not-allowed');
                btnText.innerText = '音声入力非対応ブラウザ';
                return;
            }

            const recognition = new SpeechRecognition();
            recognition.lang = 'ja-JP';
            recognition.interimResults = false;
            recognition.continuous = false; // 1文ごとに区切って入力（誤認識＆ノイズ対策）

            let isRecognizing = false;

            startBtn.addEventListener('click', function () {
                if (isRecognizing) {
                    recognition.stop();
                } else {
                    recognition.start();
                }
            });

            // 音声認識スタート時
            recognition.onstart = function () {
                isRecognizing = true;
                speechStatus.classList.remove('hidden');
                
                // ボタンのデザインを「停止（赤）」に変更
                startBtn.classList.replace('text-blue-600', 'text-white');
                startBtn.classList.replace('border-blue-500', 'border-red-500');
                startBtn.classList.add('bg-red-500', 'hover:bg-red-600');
                micIcon.innerText = '🛑';
                btnText.innerText = '音声入力を終了';
            };

            // 音声認識終了時
            recognition.onend = function () {
                isRecognizing = false;
                speechStatus.classList.add('hidden');
                
                // ボタンのデザインを「開始（青）」に戻す
                startBtn.classList.replace('text-white', 'text-blue-600');
                startBtn.classList.replace('border-red-500', 'border-blue-500');
                startBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
                micIcon.innerText = '🎤';
                btnText.innerText = '音声入力を開始';
            };

            // 文字起こし成功時
            recognition.onresult = function (event) {
                const resultText = event.results[0][0].transcript;
                
                // 既存の文字がある場合は上書きせず、スペースを空けて追記する現場親切設計
                if (remarksTextarea.value.trim() !== "") {
                    remarksTextarea.value = remarksTextarea.value.trim() + " " + resultText;
                } else {
                    remarksTextarea.value = resultText;
                }
            };

            // エラーハンドリング
            recognition.onerror = function (event) {
                console.error('音声認識エラー:', event.error);
                if (event.error === 'not-allowed') {
                    alert('マイクの使用が許可されていません。ブラウザのアドレスバー横の鍵マークなどからマイクを許可してください。');
                }
            };
        });
    </script>
</x-app-layout>