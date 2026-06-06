<?php

use Livewire\Volt\Component;
use App\Models\Inventory; // ここでモデルを読み込みます
use App\Models\Manual;
use App\Models\TroubleReport;

new class extends Component {
    public $inventory;
    public $manuals;
    
    // 追加: フォーム用の変数
    public $description = ''; 
    public $title = '';

    public function mount() {
        $this->inventory = Inventory::all();
        $this->manuals = Manual::all();

        // 自動生成：依頼内容などが決まっていればここで行う
        // 例えば「設備名 + 日付」をデフォルトにする
        $this->title = '補修依頼: 設備A (' . date('Y-m-d') . ')';
    }

    // 追加: 保存処理
    public function save() {
        // バリデーションに title を追加）
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|min:5',
        ]);

        // データベースに保存
        TroubleReport::create([
            'title' => $this->title,
            'description' => $this->description,
            // 必要に応じてuser_idなども追加
        ]);

        // 保存後にフォームを空にする
        $this->reset('description');
        
        // 成功メッセージ
        session()->flash('message', '補修依頼を送信しました！');
    }
}; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-xl font-bold mb-4">補修依頼入力</h2>
            
            @if (session()->has('message'))
                <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">{{ session('message') }}</div>
            @endif

            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">依頼タイトル</label>
                    <input type="text" wire:model="title" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">依頼内容</label>
                    <textarea class="w-full mt-1 border-gray-300 rounded-md shadow-sm" wire:model="description"></textarea>
                    @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">送信</button>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
            <h3 class="font-bold text-yellow-800 mb-2">必要な在庫パーツ</h3>
            @foreach($inventory as $item)
                <div class="flex justify-between py-1 border-b border-yellow-100">
                    <span class="text-sm">{{ $item->name }}</span>
                    <span class="font-bold text-sm">{{ $item->quantity }}</span>
                </div>
            @endforeach
        </div>

        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h3 class="font-bold text-blue-800 mb-2">関連マニュアル</h3>
            @foreach($manuals as $manual)
                <div class="text-sm mb-2">
                    <a href="{{ $manual->url }}" target="_blank" class="text-blue-600 hover:underline">
                        ・{{ $manual->title }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>