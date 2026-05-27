<?php

declare(strict_types=1);

namespace Twsms\Internal;

use Twsms\Exception\ValidationException;

final class InputValidator
{
    public function validate(string $username, string $password, string $mobile, string $message): void
    {
        $this->validateRequired('username', $username);
        $this->validateRequired('password', $password);
        $this->validateRequired('mobile', $mobile);
        $this->validateRequired('message', $message);

        if (!preg_match('/^09\d{8}$/', $mobile)) {
            throw new ValidationException('mobile format is invalid.');
        }

        if (mb_strlen($message) > 280) {
            throw new ValidationException('message length must be <= 280 characters.');
        }
    }

    private function validateRequired(string $field, string $value): void
    {
        if (trim($value) === '') {
            throw new ValidationException($field . ' is required.');
        }
    }
}
