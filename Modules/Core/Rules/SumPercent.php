<?php

namespace Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class SumPercent implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $count = 0;

        foreach ($value as $key => $item) {
            $count = $count + $item['attributes']['percent'];
        }

        return $count === 99 || $count === 100;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The sum of the experiment percentages must be 99 or 100.';
    }
}
