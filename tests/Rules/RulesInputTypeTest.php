<?php

namespace Wttks\JaTypes\Tests\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wttks\JaTypes\Rules;

/**
 * 全ルール共通の入力の扱い（Rules\Concerns\ValidatesValueObject・v1.3.1）。
 *
 * 確かめること
 *  1. null・空文字は検証しない（required は別ルール）
 *  2. 正しい文字列は通り、正しくない文字列は VO の文言で落ちる
 *  3. 配列・真偽値・小数・オブジェクトは例外にせず入力エラーにする（以前は Array to string conversion で 500）
 *  4. 整数は受け付ける（文字列にしてから VO で検証する）
 * ⚠ 互換: 以前は (string) で通っていた Stringable オブジェクトと「整数値の float（1234567.0）」も v1.3.1 からは入力エラー
 */
class RulesInputTypeTest extends TestCase
{
    /**
     * @return array<string, array{0: ValidationRule, 1: string, 2: string}>
     */
    public static function rules(): array
    {
        return [
            'PostalCode' => [new Rules\PostalCode, '123-4567', '12-34'],
            'PhoneNumber' => [new Rules\PhoneNumber, '03-1234-5678', '123'],
            'EmailAddress' => [new Rules\EmailAddress, 'taro@example.co.jp', 'taro@'],
            'CorporateNumber' => [new Rules\CorporateNumber, '7000012050002', '1234567890123'],
            'BankCode' => [new Rules\BankCode, '0001', '12345'],
            'BranchCode' => [new Rules\BranchCode, '001', '1234'],
            'BankAccountNumber' => [new Rules\BankAccountNumber, '1234567', 'abcdefg'],
            'InvoiceNumber' => [new Rules\InvoiceNumber, 'T7000012050002', 'T1234567890123'],
            'BankAccountType' => [new Rules\BankAccountType, '普通', '定期'],
            'YuchoKigo' => [new Rules\YuchoKigo, '10023', '10020'],
            'YuchoBango' => [new Rules\YuchoBango, '12345671', 'abcdefgh'],
            'JanCode' => [new Rules\JanCode, '4901234567894', '4901234567890'],
        ];
    }

    /**
     * @return list<string> 失敗の文言（通れば空）
     */
    private function failuresOf(ValidationRule $rule, mixed $value): array
    {
        $failures = [];
        $rule->validate('field', $value, function (string $message) use (&$failures): void {
            $failures[] = $message;
        });

        return $failures;
    }

    #[DataProvider('rules')]
    public function test_空は検証せず_正しい値は通り_正しくない値は落ちる(ValidationRule $rule, string $valid, string $invalid): void
    {
        $this->assertSame([], $this->failuresOf($rule, null));
        $this->assertSame([], $this->failuresOf($rule, ''));
        $this->assertSame([], $this->failuresOf($rule, $valid), "{$valid} は通るはず");
        $this->assertNotSame([], $this->failuresOf($rule, $invalid), "{$invalid} は落ちるはず");
    }

    #[DataProvider('rules')]
    public function test_文字列以外は例外にせず入力エラーにする(ValidationRule $rule, string $valid, string $invalid): void
    {
        foreach ([['123-4567'], true, 1.5, new \stdClass] as $value) {
            $this->assertSame([':attributeは文字列で入力してください。'], $this->failuresOf($rule, $value), get_debug_type($value));
        }
    }

    /**
     * 整数も受け付ける（JSON で数値が来ても落とさない・v1.3.1 の仕様）。整数を文字列にしてから VO で検証する。
     */
    public function test_整数は文字列にしてから検証する(): void
    {
        $this->assertSame([], $this->failuresOf(new Rules\PostalCode, 1234567));
        $this->assertSame([], $this->failuresOf(new Rules\BankAccountNumber, 1234567));
        $this->assertSame([], $this->failuresOf(new Rules\JanCode, 4901234567894));
        // 先頭の 0 が落ちた整数は桁が足りないので、VO の文言で落ちる（型のエラーではない）
        $failures = $this->failuresOf(new Rules\PostalCode, 600001);
        $this->assertNotSame([], $failures);
        $this->assertNotSame([':attributeは文字列で入力してください。'], $failures);
    }
}
