<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\YuchoBango;
use Wttks\JaTypes\ValueObjects\YuchoKigo;

class YuchoTest extends TestCase
{
    // --- YuchoKigo ---

    public function test_有効な記号を受け入れる(): void
    {
        // 10020: 1+0+0+2=3, 3%9=3, 末尾=0 → 不一致
        // 10010: 1+0+0+1=2, 2%9=2, 末尾=0 → 不一致
        // 正しい例: 1+0+0+2=3, 末尾3 → 10023
        $kigo = YuchoKigo::of('10023');
        $this->assertSame('10023', $kigo->value());
    }

    public function test_記号チェックデジット不一致は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        YuchoKigo::of('10020'); // 1+0+0+2=3, 末尾0≠3
    }

    public function test_記号4桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        YuchoKigo::of('1234');
    }

    // --- YuchoBango ---

    public function test_8桁番号を受け入れる(): void
    {
        $bango = YuchoBango::of('12345678');
        $this->assertSame('12345678', $bango->value());
    }

    public function test_短い番号は0埋めする(): void
    {
        $bango = YuchoBango::of('12345');
        $this->assertSame('00012345', $bango->value());
    }

    public function test_9桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        YuchoBango::of('123456789');
    }
}
