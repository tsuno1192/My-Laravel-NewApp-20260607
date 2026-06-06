<?php

namespace App\Services;

use Google_client;
use Google_Service_Sheets;

class GoogleSheetsService
{
    protected $client;
    protected $service;

   public function __construct()
    {
        $this->client = new Google_Client();
        // .envで設定したパスを読み込みます
        $this->client->setAuthConfig(env('GOOGLE_APPLICATION_CREDENTIALS'));
        $this->client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $this->service = new Google_Service_Sheets($this->client);
    }

    public function readSheet(string $spreadsheetId, string $range):array
    {
        // getValues() が null を返す可能性があるため、安全のため ?? [] を追加しています
        return $this->service->spreadsheets_values->get($spreadsheetId, $range)->getValues()??[];
    }
}