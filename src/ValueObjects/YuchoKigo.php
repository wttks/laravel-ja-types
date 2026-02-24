<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * ゆうちょ銀行 記号 ValueObject
 *
 * 通帳左上の5桁数字（例: 10020）
 * 末尾がチェックデジット（mod 9 アルゴリズム）
 */
final class YuchoKigo
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);

        if (! preg_match('/^\d{5}$/', $digits)) {
            throw new InvalidArgumentException("ゆうちょ記号は5桁の数字で入力してください: {$value}");
        }

        if (! self::isValidCheckDigit($digits)) {
            throw new InvalidArgumentException("ゆうちょ記号のチェックデジットが正しくありません: {$value}");
        }

        return new self($digits);
    }

    /**
     * ゆうちょ記号チェックデジット（mod 9）
     * 1〜4桁目の合計 mod 9 = 5桁目
     */
    private static function isValidCheckDigit(string $digits): bool
    {
        $sum = 0;
        for ($i = 0; $i < 4; $i++) {
            $sum += (int) $digits[$i];
        }
        $expected = $sum % 9;

        return (int) $digits[4] === $expected;
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
