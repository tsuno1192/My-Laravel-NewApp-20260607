<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .container { padding: 20px; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; }
        .header { font-size: 18px; font-weight: bold; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #3182ce; }
        .alert-header { border-bottom: 2px solid #e53e3e; color: #e53e3e; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table th, .info-table td { padding: 10px; border: 1px solid #eee; text-align: left; }
        .info-table th { background-color: #f7fafc; width: 30%; }
        .badge-normal { color: #2f855a; font-weight: bold; }
        .badge-anomaly { color: #c53030; font-weight: bold; background-color: #fff5f5; padding: 2px 6px; border-radius: 4px; }
        .footer { font-size: 12px; color: #777; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        📋 日常点検結果のお知らせ
    </div>

    <p>以下の設備にて日常点検が実施され、結果が送信されました。</p>

    <table class="info-table">
        <tr>
            <th>対象設備</th>
            <td><strong>{{ $equipment->name }}</strong></td>
        </tr>
        <tr>
            <th>点検日時</th>
            <td>{{ now()->format('Y-m-d H:i:s') }}</td>
        </tr>
        <tr>
            <th>点検者</th>
            <td>{{ auth()->user()->name ?? '現場作業員' }}</td>
        </tr>
    </table>

    <h3>■ 点検結果（ピックアップ）</h3>
    <table class="info-table">
        @php
            $itemLabels = [
                'item_1' => '機器振動', 'item_2' => '異音/異臭', 'item_3' => '状態温度',
                'item_4' => '電圧/周波数', 'item_5' => '電流/抵抗値', 'item_6' => 'プロセス値',
                'item_7' => 'グリス等残量', 'item_8' => '油漏洩有無', 'item_9' => '薬品残量', 'item_10' => '薬品漏洩'
            ];
        @endphp

        @foreach($itemLabels as $key => $label)
            @if(isset($data[$key]))
                <tr>
                    <th>{{ $label }}</th>
                    <td>
                        @if($data[$key] === '異常あり')
                            <span class="badge-anomaly">✕ 異常あり</span>
                        @else
                            <span class="badge-normal">〇 異常なし</span>
                        @endif
                    </td>
                </tr>
            @endif
        @endforeach
    </table>

    @if(!empty($data['notes']))
        <h3>💡 備考（特記事項）</h3>
        <div style="background: #f7fafc; padding: 15px; border-radius: 5px; border: 1px solid #e2e8f0;">
            {!! nl2br(e($data['notes'])) !!}
        </div>
    @endif

    <div class="footer">
        ※本メールはシステムより自動送信されています。
    </div>
</div>

</body>
</html>