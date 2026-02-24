<?php

namespace Wttks\JaTypes\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;
use Wttks\JaTypes\ValueObjects\YuchoKigo as YuchoKigoVO;

class YuchoKigo implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        try {
            YuchoKigoVO::of((string) $value);
        } catch (InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
