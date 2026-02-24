<?php

namespace Wttks\JaTypes\ValueObjects;

use InvalidArgumentException;

/**
 * 銀行口座種別 ValueObject
 *
 * 普通(1) / 当座(2) / 貯蓄(4)
 */
final class BankAccountType
{
    /** 口座種別コードと名称のマップ */
    private const TYPES = [
        '1' => '普通',
        '2' => '当座',
        '4' => '貯蓄',
    ];

    private function __construct(
        private readonly string $code,
        private readonly string $label,
    ) {}

    public static function of(string $value): self
    {
        $v = mb_convert_kana(trim($value), 'as');

        // コード指定（1/2/4）
        if (isset(self::TYPES[$v])) {
            return new self($v, self::TYPES[$v]);
        }

        // 名称指定（普通/当座/貯蓄）
        $byLabel = array_search($v, self::TYPES, true);
        if ($byLabel !== false) {
            return new self($byLabel, self::TYPES[$byLabel]);
        }

        $valid = implode('/', array_values(self::TYPES));
        throw new InvalidArgumentException("口座種別は {$valid} のいずれかで入力してください: {$value}");
    }

    /** コード（1/2/4）で返す */
    public function code(): string
    {
        return $this->code;
    }

    /** 名称（普通/当座/貯蓄）で返す */
    public function label(): string
    {
        return $this->label;
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }

    public function __toString(): string
    {
        return $this->label;
    }
}
