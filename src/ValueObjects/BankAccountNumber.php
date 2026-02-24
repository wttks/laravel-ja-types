<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * 銀行口座番号 ValueObject
 *
 * 現在の標準は7桁だが、古い口座では桁数が少ない場合がある。
 * 1〜7桁の数字を受け入れ、左0埋めで7桁に正規化して保持する。
 * ゆうちょ銀行の記号・番号形式は対象外。
 */
final class BankAccountNumber
{
    private const MAX_DIGITS = 7;

    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);

        if ($digits === '' || strlen($digits) > self::MAX_DIGITS) {
            throw new InvalidArgumentException(
                "口座番号は1〜".self::MAX_DIGITS."桁の数字で入力してください: {$value}"
            );
        }

        // 左0埋めで7桁に正規化
        $normalized = str_pad($digits, self::MAX_DIGITS, '0', STR_PAD_LEFT);

        return new self($normalized);
    }

    /** 0埋め7桁で返す（例: 0001234） */
    public function value(): string
    {
        return $this->value;
    }

    /** 先頭の0を除いた実際の桁数で返す（例: 1234） */
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
