<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\PhoneNumber;

class PhoneNumberTest extends TestCase
{
    public function test_10桁番号を受け入れる(): void
    {
        $p = PhoneNumber::of('03-1234-5678');
        $this->assertSame('0312345678', $p->digits());
    }

    public function test_11桁番号を受け入れる(): void
    {
        $p = PhoneNumber::of('090-1234-5678');
        $this->assertSame('09012345678', $p->digits());
    }

    public function test_全角数字を受け入れる(): void
    {
        $p = PhoneNumber::of('０３－１２３４－５６７８');
        $this->assertSame('0312345678', $p->digits());
    }

    public function test_括弧形式を受け入れる(): void
    {
        $p = PhoneNumber::of('(03)1234-5678');
        $this->assertSame('0312345678', $p->digits());
    }

    public function test_0始まり以外は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PhoneNumber::of('1234567890');
    }

    public function test_9桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PhoneNumber::of('012345678');
    }

    public function test_fromStorage_は検証なしで正準値をそのまま保持する(): void
    {
        $p = PhoneNumber::fromStorage('03-1234-5678');
        $this->assertSame('03-1234-5678', $p->value());
        $this->assertSame('0312345678', $p->digits());
    }
}
