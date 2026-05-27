<?php

declare(strict_types=1);

namespace Twsms;

use Twsms\Exception\TransportException;
use Twsms\Model\SendResult;
use Twsms\Internal\CurlHttpClient;
use Twsms\Internal\HttpClient;
use Twsms\Internal\InputValidator;

final class TwsmsSender
{
    private const API_URL = 'http://api.twsms.com/json/sms_send.php';

    private HttpClient $httpClient;
    private InputValidator $validator;

    public function __construct(?HttpClient $httpClient = null, ?InputValidator $validator = null)
    {
        $this->httpClient = $httpClient ?? new CurlHttpClient();
        $this->validator = $validator ?? new InputValidator();
    }

    public function send(string $username, string $password, string $mobile, string $message): SendResult
    {
        $this->validator->validate($username, $password, $mobile, $message);

        $payload = [
            'username' => $username,
            'password' => $password,
            'mobile' => $mobile,
            'longsms' => 'Y',
            'message' => urlencode($message),
        ];

        $raw = $this->httpClient->post(self::API_URL, $payload);
        $decoded = json_decode($raw, true);

        if (!is_array($decoded) || !isset($decoded['code'], $decoded['text'])) {
            throw new TransportException('Invalid JSON response from TWsms.');
        }

        $messageId = isset($decoded['msgid']) ? (string) $decoded['msgid'] : null;

        return new SendResult((string) $decoded['code'], (string) $decoded['text'], $messageId);
    }
}
