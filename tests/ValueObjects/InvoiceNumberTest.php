<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\InvoiceNumber;

class InvoiceNumberTest extends TestCase
{
    public function test_有効なインボイス番号を受け入れる(): void
    {
        $valid = 'T' . self::buildValid('234567890123');
        $inv = InvoiceNumber::of($valid);
        $this->assertStringStartsWith('T', $inv->value());
        $this->assertSame(13, strlen($inv->digits()));
    }

    public function test_小文字tプレフィックスを受け入れる(): void
    {
        $valid = 't' . self::buildValid('234567890123');
        $inv = InvoiceNumber::of($valid);
        $this->assertSame('T', substr($inv->value(), 0, 1));
    }

    public function test_Tなし13桁を受け入れる(): void
    {
        $valid = self::buildValid('234567890123');
        $inv = InvoiceNumber::of($valid);
        $this->assertSame('T' . $valid, $inv->value());
    }

    public function test_チェックデジット不一致は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $valid = self::buildValid('234567890123');
        $wrong = ((int)$valid[0] === 9 ? '0' : '9') . substr($valid, 1);
        InvoiceNumber::of('T' . $wrong);
    }

    public function test_12桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        InvoiceNumber::of('T123456789012');
    }

    private static function buildValid(string $base12): string
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $weight = ((12 - $i) % 2 === 0) ? 2 : 1;
            $sum += (int)$base12[$i] * $weight;
        }
        $check = 9 - ($sum % 9);
        if ($check === 9) $check = 0;
        return (string)$check . $base12;
    }
}
