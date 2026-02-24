<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * 法人番号 ValueObject
 *
 * 13桁数字・国税庁チェックデジットアルゴリズムで検証。
 * 参考: https://www.nta.go.jp/taxes/tetsuzuki/mynumber/houjin/02.htm
 */
final class CorporateNumber
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        // 全角→半角・ハイフン・スペース除去
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);

        if (strlen($digits) !== 13) {
            throw new InvalidArgumentException("法人番号は13桁の数字で入力してください: {$value}");
        }

        if (! self::isValidCheckDigit($digits)) {
            throw new InvalidArgumentException("法人番号のチェックデジットが正しくありません: {$value}");
        }

        return new self($digits);
    }

    /**
     * チェックデジット検証（国税庁アルゴリズム）
     *
     * - 1桁目がチェックデジット
     * - 2〜13桁目（右から数えて12桁）に重みを掛けて合計
     * - 重み: 右から奇数番目=1、偶数番目=2
     * - チェックデジット = 9 - (合計 mod 9)、9になる場合は0
     */
    private static function isValidCheckDigit(string $digits): bool
    {
        $check = (int) $digits[0];
        $sum = 0;

        for ($i = 1; $i <= 12; $i++) {
            $d = (int) $digits[$i];
            // 右から数えた位置: 13 - i
            $weight = ((13 - $i) % 2 === 0) ? 2 : 1;
            $sum += $d * $weight;
        }

        $expected = 9 - ($sum % 9);
        if ($expected === 9) {
            $expected = 0;
        }

        return $check === $expected;
    }

    /** 数字のみ13桁で返す */
    public function value(): string
    {
        return $this->value;
    }

    /** X-XXXX-XXXX-XXXX 形式で返す */
    public function formatted(): string
    {
        return substr($this->value, 0, 1).'-'
            .substr($this->value, 1, 4).'-'
            .substr($this->value, 5, 4).'-'
            .substr($this->value, 9, 4);
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
