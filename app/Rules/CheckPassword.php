<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CheckPassword implements Rule
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $name)
    {
        $this->user = UserRepository::getByName($name);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Hash::check($value, $this->user->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Неверный логин или пароль';
    }
}
