<?php

namespace Wttks\JaTypes\Rules\Concerns;

use Closure;
use InvalidArgumentException;

/**
 * 値オブジェクトの生成（VO::of）で検証するルールの共通処理。状態は持たない。
 *
 * - null・空文字は検証しない（必須かどうかは required 等の別ルールで決める）
 * - 文字列と整数だけを受け付ける。配列・真偽値・小数・オブジェクトは入力エラーにする
 *   （`email[]=a` のような送信で `(string)` キャストが Array to string conversion になり 500 になっていた・v1.3.1）
 * - 生成で InvalidArgumentException が出たら、その文言で $fail する
 */
trait ValidatesValueObject
{
    /**
     * @param  Closure(string): mixed  $make  文字列から値オブジェクトを作る（失敗なら InvalidArgumentException）
     */
    protected function validateValueObject(mixed $value, Closure $fail, Closure $make): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value) && ! is_int($value)) {
            $fail(':attributeは文字列で入力してください。');

            return;
        }

        try {
            $make((string) $value);
        } catch (InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
