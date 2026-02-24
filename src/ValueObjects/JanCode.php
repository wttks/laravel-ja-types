<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * JANコード（EAN-8 / EAN-13）ValueObject
 *
 * 8桁または13桁の数字。モジュロ10加重平均でチェックデジット検証。
 * 日本のJANコードは 49 または 45 始まりだが、ここでは桁数とチェックデジットのみ検証。
 */
final class JanCode
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);
        $len = strlen($digits);

        if ($len !== 8 && $len !== 13) {
            throw new InvalidArgumentException("JANコードは8桁または13桁の数字で入力してください: {$value}");
        }

        if (! self::isValidCheckDigit($digits)) {
            throw new InvalidArgumentException("JANコードのチェックデジットが正しくありません: {$value}");
        }

        return new self($digits);
    }

    /**
     * EAN チェックデジット検証（モジュロ10加重平均）
     * 右から2番目の桁から左へ: 奇数番目×1、偶数番目×3
     * (右端=チェックデジット は検証対象外)
     */
    private static function isValidCheckDigit(string $digits): bool
    {
        $len = strlen($digits);
        $sum = 0;

        for ($i = 0; $i < $len - 1; $i++) {
            $d = (int) $digits[$i];
            // 右から数えた位置（チェックデジットを除く）
            $posFromRight = $len - 1 - $i;
            $weight = ($posFromRight % 2 === 0) ? 3 : 1;
            $sum += $d * $weight;
        }

        $remainder = $sum % 10;
        $expected = $remainder === 0 ? 0 : 10 - $remainder;

        return (int) $digits[$len - 1] === $expected;
    }

    /** 数字のみで返す */
    public function value(): string
    {
        return $this->value;
    }

    /** 桁数（8 or 13）を返す */
    public function length(): int
    {
        return strlen($this->value);
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
