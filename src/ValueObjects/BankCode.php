<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * 金融機関コード（銀行コード）ValueObject
 *
 * 全国銀行協会が定める4桁数字。
 */
final class BankCode
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);

        // 4桁未満は0埋め（例: "1" → "0001"）
        $digits = str_pad($digits, 4, '0', STR_PAD_LEFT);

        if (! preg_match('/^\d{4}$/', $digits)) {
            throw new InvalidArgumentException("金融機関コードは4桁の数字で入力してください: {$value}");
        }

        return new self($digits);
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
