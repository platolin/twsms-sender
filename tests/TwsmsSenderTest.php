<?php

declare(strict_types=1);

namespace Twsms\Tests;

use PHPUnit\Framework\TestCase;
use Twsms\Exception\TransportException;
use Twsms\Exception\ValidationException;
use Twsms\Internal\HttpClient;
use Twsms\TwsmsSender;

final class TwsmsSenderTest extends TestCase
{
    public function testSendReturnsSuccessResult(): void
    {
        $client = new FakeHttpClient('{"code":"00000","text":"ok","msgid":"123"}');
        $sender = new TwsmsSender($client);

        $result = $sender->send('user', 'pass', '0912345678', 'hello');

        self::assertSame('00000', $result->code);
        self::assertSame('ok', $result->text);
        self::assertSame('123', $result->messageId);
        self::assertTrue($result->isSuccess());
    }

    public function testSendThrowsOnEmptyUsername(): void
    {
        $sender = new TwsmsSender(new FakeHttpClient('{"code":"00000","text":"ok"}'));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('username is required.');

        $sender->send('', 'pass', '0912345678', 'hello');
    }

    public function testSendThrowsOnInvalidMobile(): void
    {
        $sender = new TwsmsSender(new FakeHttpClient('{"code":"00000","text":"ok"}'));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('mobile format is invalid.');

        $sender->send('user', 'pass', '123', 'hello');
    }

    public function testSendThrowsOnTooLongMessage(): void
    {
        $sender = new TwsmsSender(new FakeHttpClient('{"code":"00000","text":"ok"}'));
        $longMessage = str_repeat('a', 281);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('message length must be <= 280 characters.');

        $sender->send('user', 'pass', '0912345678', $longMessage);
    }

    public function testSendThrowsOnInvalidUpstreamPayload(): void
    {
        $sender = new TwsmsSender(new FakeHttpClient('{"foo":"bar"}'));

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Invalid JSON response from TWsms.');

        $sender->send('user', 'pass', '0912345678', 'hello');
    }
}

final class FakeHttpClient implements HttpClient
{
    public function __construct(private readonly string $response)
    {
    }

    public function post(string $url, array $payload): string
    {
        return $this->response;
    }
}
