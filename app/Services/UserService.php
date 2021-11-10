<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Авторизует пользователя в системе
     *
     * @param string $name
     * @param string $password
     *
     * @return User
     * @throws Exception
     */
    public static function login(string $name, string $password): User
    {
        $user = UserRepository::getByName($name);
        if (!Hash::check($password, $user->password)) {
            throw new Exception('Неверный логин или пароль', 422);
        }

        return UserRepository::login($user);
    }
}
