<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\BankAccountType;

class BankAccountTypeTest extends TestCase
{
    public function test_コード1で普通口座を受け入れる(): void
    {
        $t = BankAccountType::of('1');
        $this->assertSame('1', $t->code());
        $this->assertSame('普通', $t->label());
    }

    public function test_コード2で当座口座を受け入れる(): void
    {
        $t = BankAccountType::of('2');
        $this->assertSame('当座', $t->label());
    }

    public function test_コード4で貯蓄口座を受け入れる(): void
    {
        $t = BankAccountType::of('4');
        $this->assertSame('貯蓄', $t->label());
    }

    public function test_名称で指定できる(): void
    {
        $t = BankAccountType::of('普通');
        $this->assertSame('1', $t->code());
    }

    public function test_無効なコードは拒否(): void
    {
        $this->expectException(InvalidArgumentException::class);
        BankAccountType::of('3');
    }
}
