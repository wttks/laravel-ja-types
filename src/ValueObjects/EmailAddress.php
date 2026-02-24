<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * メールアドレス ValueObject
 *
 * filter_var(FILTER_VALIDATE_EMAIL) で検証。
 * 保持形式: 小文字正規化済み
 */
final class EmailAddress
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $value = mb_strtolower(trim($value));

        if ($value === '') {
            throw new InvalidArgumentException('メールアドレスは空にできません');
        }

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("メールアドレスの形式が正しくありません: {$value}");
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
