<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * 郵便番号 ValueObject
 *
 * 受け入れ: 1234567 / 123-4567（〒記号・全角も可）
 * 保持形式: 数字のみ7桁（例: 1234567）
 */
final class PostalCode
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        // 全角→半角・〒除去・数字以外除去
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);

        if (! preg_match('/^\d{7}$/', $digits)) {
            throw new InvalidArgumentException("郵便番号は7桁の数字で入力してください: {$value}");
        }

        return new self($digits);
    }

    /** 数字のみ7桁で返す（例: 1234567） */
    public function value(): string
    {
        return $this->value;
    }

    /** 123-4567 形式で返す */
    public function formatted(): string
    {
        return substr($this->value, 0, 3).'-'.substr($this->value, 3);
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
