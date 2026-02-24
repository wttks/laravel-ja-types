<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * 電話番号 ValueObject（国内番号）
 *
 * 受け入れ: 0X-XXXX-XXXX / 0XX-XXX-XXXX など（全角・括弧も可）
 * 保持形式: 正規化済みハイフン区切り（例: 03-1234-5678）
 * 制約: 0始まり・数字10〜11桁
 */
final class PhoneNumber
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        // 全角→半角
        $v = mb_convert_kana(trim($value), 'as');
        // 括弧・スペース除去
        $v = preg_replace('/[（(）)\s　]/', '', $v);
        // 全角ハイフン類 → 半角ハイフン
        $v = preg_replace('/[－−–—ー]/', '-', $v);
        // 数字とハイフン以外を除去
        $v = preg_replace('/[^\d\-]/', '', $v);
        $v = trim($v, '-');
        $v = preg_replace('/-{2,}/', '-', $v);

        $digits = str_replace('-', '', $v);

        if (! preg_match('/^0\d{9,10}$/', $digits)) {
            throw new InvalidArgumentException("電話番号の形式が正しくありません: {$value}");
        }

        return new self($v);
    }

    /** ハイフン区切り形式で返す（例: 03-1234-5678） */
    public function value(): string
    {
        return $this->value;
    }

    /** 数字のみで返す（例: 0312345678） */
    public function digits(): string
    {
        return str_replace('-', '', $this->value);
    }

    public function equals(self $other): bool
    {
        return $this->digits() === $other->digits();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
