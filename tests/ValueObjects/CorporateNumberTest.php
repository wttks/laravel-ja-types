<?php

namespace Wttks\JaTypes\Tests\ValueObjects;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\ValueObjects\CorporateNumber;

class CorporateNumberTest extends TestCase
{
    // 国税庁公式サンプル: 1234567890123
    // チェックデジット検証のため、実在する法人番号を使用
    // 例: トヨタ自動車 4800030000001（架空値でチェックデジット一致するものを使用）

    public function test_有効な法人番号を受け入れる(): void
    {
        // チェックデジット: 9 - (sum mod 9) で計算済みの値
        // 2345678901234 → sum計算して先頭に正しいチェックデジットを付与
        $valid = self::buildValid('234567890123');
        $cn = CorporateNumber::of($valid);
        $this->assertSame($valid, $cn->value());
    }

    public function test_formatted_でハイフン区切りを返す(): void
    {
        $valid = self::buildValid('234567890123');
        $cn = CorporateNumber::of($valid);
        $this->assertMatchesRegularExpression('/^\d{1}-\d{4}-\d{4}-\d{4}$/', $cn->formatted());
    }

    public function test_12桁は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        CorporateNumber::of('123456789012');
    }

    public function test_チェックデジット不一致は無効(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // 先頭を意図的に間違える
        $valid = self::buildValid('234567890123');
        $wrong = ((int) $valid[0] === 9 ? '0' : '9').substr($valid, 1);
        CorporateNumber::of($wrong);
    }

    public function test_ハイフン区切りを受け入れる(): void
    {
        $valid = self::buildValid('234567890123');
        $hyphenated = substr($valid, 0, 1).'-'.substr($valid, 1, 4).'-'.substr($valid, 5, 4).'-'.substr($valid, 9, 4);
        $cn = CorporateNumber::of($hyphenated);
        $this->assertSame($valid, $cn->value());
    }

    /** テスト用: 12桁の基礎番号から正しいチェックデジット付き13桁を生成 */
    private static function buildValid(string $base12): string
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $d = (int) $base12[$i];
            // 配列インデックス i に対する右からの位置: 12 - i
            $weight = ((12 - $i) % 2 === 0) ? 2 : 1;
            $sum += $d * $weight;
        }
        $check = 9 - ($sum % 9);
        if ($check === 9) {
            $check = 0;
        }

        return (string) $check.$base12;
    }
}
