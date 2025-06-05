<?php

namespace App\Rules;

use App\Enum\Status;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateStatus implements ValidationRule
{
    protected string $group;

    public function __construct(string $group)
    {
        $this->group = $group;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
    public function passes($attribute, $value): bool
    {
        $validLabels = array_map('strtoupper', array_values(Status::options($this->group)));
        return in_array(strtoupper($value), $validLabels);
    }

    public function message(): string
    {
        return 'The selected :attribute is invalid.';
    }
}
