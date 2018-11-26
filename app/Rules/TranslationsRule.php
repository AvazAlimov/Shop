<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Rules;

use App\Language;
use Illuminate\Contracts\Validation\Rule;

class TranslationsRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($value as $code => $translation) {
            if (Language::where("code", "=", $code)->count() == 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The language not found.';
    }
}
