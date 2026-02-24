<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\BankAccountNumber;

class BankAccountNumberTest extends TestCase
{
    public function test_7桁を受け入れる(): void
    {
        $b = BankAccountNumber::of('1234567');
        $this->assertSame('1234567', $b->value());
    }

    public function test_桁数が少ない場合は0埋めする(): void
    {
        $b = BankAccountNumber::of('1234');
        $this->assertSame('0001234', $b->value());
    }

    public function test_1桁を受け入れる(): void
    {
        $b = BankAccountNumber::of('1');
        $this->assertSame('0000001', $b->value());
    }

    public function test_全角数字を受け入れる(): void
    {
        $b = BankAccountNumber::of('１２３４５６７');
        $this->assertSame('1234567', $b->value());
    }

    public function test_trimmed_で先頭ゼロを除去して返す(): void
    {
        $b = BankAccountNumber::of('1234');
        $this->assertSame('1234', $b->trimmed());
    }

    public function test_8桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BankAccountNumber::of('12345678');
    }

    public function test_空文字は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BankAccountNumber::of('');
    }
}
