<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * インボイス番号（適格請求書発行事業者登録番号）ValueObject
 *
 * 形式: T + 13桁数字
 * チェックデジット: 法人番号と同じアルゴリズム（国税庁）
 */
final class InvoiceNumber
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');
        // T/t プレフィックス除去
        $body = preg_replace('/^[Tt]/', '', $v);
        $digits = preg_replace('/[^\d]/', '', $body);

        if (strlen($digits) !== 13) {
            throw new InvalidArgumentException("インボイス番号は T + 13桁の数字で入力してください: {$value}");
        }

        if (! self::isValidCheckDigit($digits)) {
            throw new InvalidArgumentException("インボイス番号のチェックデジットが正しくありません: {$value}");
        }

        return new self('T' . $digits);
    }

    /**
     * チェックデジット検証（法人番号と同じアルゴリズム）
     * 1桁目がチェックデジット、右から奇数番目=1、偶数番目=2 の重み
     */
    private static function isValidCheckDigit(string $digits): bool
    {
        $check = (int) $digits[0];
        $sum = 0;

        for ($i = 1; $i <= 12; $i++) {
            $d = (int) $digits[$i];
            $weight = ((13 - $i) % 2 === 0) ? 2 : 1;
            $sum += $d * $weight;
        }

        $expected = 9 - ($sum % 9);
        if ($expected === 9) {
            $expected = 0;
        }

        return $check === $expected;
    }

    /** T + 13桁で返す（例: T1234567890123） */
    public function value(): string
    {
        return $this->value;
    }

    /** 数字13桁のみで返す */
    public function digits(): string
    {
        return substr($this->value, 1);
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
