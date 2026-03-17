<?php

namespace app\components;

use Yii;
use yii\base\Component;

class SmsService extends Component
{
    public string $apiKey = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
    public string $sender = 'INFORM';
    public bool $testMode = true;

    private const string API_URL = 'https://smspilot.ru/api.php';

    public function send(string $phone, string $message): array
    {
        $params = [
            'send' => $message,
            'to' => $this->normalizePhone($phone),
            'apikey' => $this->apiKey,
            'from' => $this->sender,
            'format' => 'json',
        ];

        if ($this->testMode) {
            $params['test'] = 1;
        }

        $url = self::API_URL . '?' . http_build_query($params);

        $response = @file_get_contents($url);

        if ($response === false) {
            Yii::error("SMS send failed: Could not connect to API", 'sms');
            return ['success' => false, 'error' => 'Connection failed'];
        }

        $result = json_decode($response, true);

        if (isset($result['error'])) {
            Yii::error("SMS send failed: " . $result['error']['description'], 'sms');
            return ['success' => false, 'error' => $result['error']['description']];
        }

        Yii::info("SMS sent to {$phone}: {$message}", 'sms');
        return ['success' => true, 'data' => $result];
    }

    public function sendToMany(array $phones, string $message): array
    {
        $results = [];
        foreach ($phones as $phone) {
            $results[$phone] = $this->send($phone, $message);
        }
        return $results;
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '8') && strlen($phone) === 11) {
            $phone = '7' . substr($phone, 1);
        }

        return $phone;
    }
}
