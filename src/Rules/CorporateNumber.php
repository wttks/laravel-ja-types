<?php

namespace Wttks\JaTypes\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Wttks\JaTypes\Rules\Concerns\ValidatesValueObject;
use Wttks\JaTypes\ValueObjects\CorporateNumber as CorporateNumberVO;

class CorporateNumber implements ValidationRule
{
    use ValidatesValueObject;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->validateValueObject($value, $fail, static fn (string $v) => CorporateNumberVO::of($v));
    }
}
