<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\PostalCode;

class PostalCodeTest extends TestCase
{
    public function test_数字7桁を受け入れる(): void
    {
        $p = PostalCode::of('1234567');
        $this->assertSame('1234567', $p->value());
    }

    public function test_ハイフンあり形式を受け入れる(): void
    {
        $p = PostalCode::of('123-4567');
        $this->assertSame('1234567', $p->value());
    }

    public function test_全角数字を受け入れる(): void
    {
        $p = PostalCode::of('１２３４５６７');
        $this->assertSame('1234567', $p->value());
    }

    public function test_〒記号を除去する(): void
    {
        $p = PostalCode::of('〒123-4567');
        $this->assertSame('1234567', $p->value());
    }

    public function test_formatted_でハイフン区切り形式を返す(): void
    {
        $p = PostalCode::of('1234567');
        $this->assertSame('123-4567', $p->formatted());
    }

    public function test_6桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PostalCode::of('123456');
    }

    public function test_8桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PostalCode::of('12345678');
    }

    public function test_空文字は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PostalCode::of('');
    }

    public function test_fromStorage_は検証なしで正準値をそのまま保持する(): void
    {
        $p = PostalCode::fromStorage('1234567');
        $this->assertSame('1234567', $p->value());
        $this->assertSame('123-4567', $p->formatted());
    }
}
