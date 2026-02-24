<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\JanCode;

class JanCodeTest extends TestCase
{
    public function test_有効な13桁JANコードを受け入れる(): void
    {
        // 4901234567894 (実在サンプル形式)
        $jan = JanCode::of('4901234567894');
        $this->assertSame('4901234567894', $jan->value());
        $this->assertSame(13, $jan->length());
    }

    public function test_有効な8桁JANコードを受け入れる(): void
    {
        // 49012341: 4*1+9*3+0*1+1*3+2*1+3*3+4*1=4+27+0+3+2+9+4=49, 49%10=9, 10-9=1
        $jan = JanCode::of('49012341');
        $this->assertSame('49012341', $jan->value());
        $this->assertSame(8, $jan->length());
    }

    public function test_チェックデジット不一致は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        JanCode::of('4901234567890'); // 末尾0は不正
    }

    public function test_12桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        JanCode::of('490123456789');
    }

    public function test_全角数字を受け入れる(): void
    {
        $jan = JanCode::of('４９０１２３４５６７８９４');
        $this->assertSame('4901234567894', $jan->value());
    }
}
