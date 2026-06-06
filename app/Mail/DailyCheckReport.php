<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyCheckReport extends Mailable
{
    use Queueable, SerializesModels;

    // ブレードに渡すためのプロパティ
    public $equipment;
    public $data;
    public $hasRecord;

    /**
     * Create a new message instance.
     */
    public function __construct($equipment, $data)
    {
        $this->equipment = $equipment;
        $this->data = $data;
    }

    /**
     * メールの件名（タイトル）を設定
     */
    public function envelope(): Envelope
    {
        // 10項目の中に「異常あり」が含まれているかチェック
        $hasAnomaly = false;
        foreach ($this->data as $key => $value) {
            if (strpos($key, 'item_') === 0 && $value === '異常あり') {
                $hasAnomaly = true;
                break;
            }
        }

        $subject = $hasAnomaly 
            ? "【⚠️異常あり】{$this->equipment->name} の日常点検報告" 
            : "【正常】{$this->equipment->name} の日常点検報告";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * メールの本文（テンプレート）を指定
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_check_report',
        );
    }
}
