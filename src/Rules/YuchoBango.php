<?php

namespace Wttks\JaTypes\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;
use Wttks\JaTypes\ValueObjects\YuchoBango as YuchoBangoVO;

class YuchoBango implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        try {
            YuchoBangoVO::of((string) $value);
        } catch (InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}
