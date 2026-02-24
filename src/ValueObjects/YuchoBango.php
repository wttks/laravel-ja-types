<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * ゆうちょ銀行 番号 ValueObject
 *
 * 通帳右上の番号。末尾1桁はチェックデジットを除いた実質7桁 + チェック1桁 = 8桁。
 * 入力: 1〜8桁数字を受け入れ、左0埋めで8桁に正規化。
 */
final class YuchoBango
{
    private const DIGITS = 8;

    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);

        if ($digits === '' || strlen($digits) > self::DIGITS) {
            throw new InvalidArgumentException("ゆうちょ番号は1〜8桁の数字で入力してください: {$value}");
        }

        $normalized = str_pad($digits, self::DIGITS, '0', STR_PAD_LEFT);

        return new self($normalized);
    }

    /** 0埋め8桁で返す */
    public function value(): string
    {
        return $this->value;
    }

    /** 先頭0を除いた表示用 */
    public function trimmed(): string
    {
        return ltrim($this->value, '0') ?: '0';
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
