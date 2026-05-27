<?php

declare(strict_types=1);

namespace Twsms\Model;

final readonly class SendResult
{
    public function __construct(
        public string $code,
        public string $text,
        public ?string $messageId = null,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->code === '00000';
    }
}
