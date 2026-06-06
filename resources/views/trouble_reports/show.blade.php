<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>トラブル報告書</title>
    <style>
        /* 印刷時にボタンや不要なパーツを消す */
    @media print {
        .no-print { display: none !important; }
        
        /* フォームの枠線をなくして、報告書らしく見せる */
        input, textarea, select {
            border: none !important;
            background: transparent !important;
            width: 100%;
        }

        /* 印刷時のデザインを整える（CSSに追加） */
         @media print {
    h2 { border-bottom: 2px solid #333; padding-bottom: 10px; }
    label { font-weight: bold; color: #555; }
    .form-group { border-bottom: 1px solid #ccc; padding-bottom: 10px; }
}
    }

    /* 画面上の見た目 */
    .report-container { max-width: 800px; margin: auto; }
    .form-group { margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="report-container">
    <h2>トラブル報告書・補修依頼書</h2>
    
    <div class="form-group">
        <label>対象設備:</label>
        <p>{{ $report->checkRecord->equipment->name }}</p>
    </div>

    <div class="form-group">
        <label>依頼タイトル:</label>
        <p>{{ $report->title }}</p>
    </div>

    <div class="form-group">
        <label>トラブル内容・具体的な状況:</label>
        <p>{{ $report->description }}</p>
    </div>

    <button onclick="window.print()" class="btn btn-primary no-print">
        この形式でPDFとして保存・印刷
    </button>
</div>

</body>
</html>