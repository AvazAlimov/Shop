<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Rules;

use App\Category;
use Illuminate\Contracts\Validation\Rule;

class CategoryRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
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
        $parent = Category::find($value);
        if (!$parent) {
            return false;
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
        return 'Category not found';
    }
}
