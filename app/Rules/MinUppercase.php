<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class MinUppercase implements Rule
{
    protected $min;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($min = 0)
    {
        $this->min = $min;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->min <= preg_match_all('/[A-Z]/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.min_uppercase', [
            'min' => $this->min,
        ]);
    }
}
