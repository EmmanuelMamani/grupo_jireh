<?php

namespace App\Rules;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class uniqueUser implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return User::where('Usuario',$value)->where('Activo',1)->get()->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ya existe una cuenta con ese nombre de usuario';
    }
}
