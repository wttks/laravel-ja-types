<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * 支店コード（店番）ValueObject
 *
 * 3桁数字。入力が3桁未満の場合は0埋め。
 */
final class BranchCode
{
    private function __construct(private readonly string $value) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');
        $digits = preg_replace('/[^\d]/', '', $v);

        $digits = str_pad($digits, 3, '0', STR_PAD_LEFT);

        if (! preg_match('/^\d{3}$/', $digits)) {
            throw new InvalidArgumentException("支店コードは3桁の数字で入力してください: {$value}");
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
