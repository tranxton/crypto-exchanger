<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\User\GetRequest as UserGetRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest as UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class UserController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/user/{id}",
     *      operationId="get",
     *      tags={"Пользователь"},
     *      summary="Получить объект пользователя",
     *      description="Возвращает объект User",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Токен",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно выполнен",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Требуется авторизация",
     *      )
     * )
     *
     * Получение объекта пользователя
     *
     * @param int $id
     *
     * @return Response
     */
    public function get(UserGetRequest $request): Response
    {
        $id = (int) $request->validated()['id'];
        try {
            $user = User::get($id);
        } catch (\Exception $e) {
            $message = ['message' => $e->getMessage()];

            return new Response($message, 422);
        }

        return new Response(new UserResource($user));
    }

    /**
     * @OA\Post(
     *      path="/user/register",
     *      operationId="register",
     *      tags={"Пользователь"},
     *      summary="Регистрация пользователя в системе",
     *      description="Регистрирует пользователя с системе",
     *      @OA\Parameter(
     *          name="name",
     *          description="Логин",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="email",
     *          description="E-mail",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="Пароль",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="referral_link",
     *          description="Реферальная ссылка",
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно выполнен"
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Произошла непредвиденная ошибка"
     *      )
     * )
     *
     * Регистрация пользователя в системе
     *
     * @param Request $request
     *
     * @return Response
     */
    public function register(UserRegisterRequest $request): Response
    {
        $user = $request->validated();
        $user['referral_link'] ??= null;

        try {
            UserRepository::create($user['name'], $user['email'], $user['password'], $user['referral_link']);
        } catch (\Exception $e) {
            $message = ['message' => $e->getMessage()];

            return new Response($message, 500);
        }

        $message = ['message' => 'Пользователь успешно зарегистрирован'];

        return new Response($message);
    }

    /**
     * @OA\Get (
     *      path="/user/login",
     *      operationId="login",
     *      tags={"Пользователь"},
     *      summary="Авторизация пользователя в системе",
     *      description="Возвращает авторизационный токен",
     *      @OA\Parameter(
     *          name="login",
     *          description="Логин",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="password",
     *          description="Пароль",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешно выполнен",
     *          @OA\JsonContent(ref="#/components/schemas/Auth")
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Произошла непредвиденная ошибка"
     *      )
     * )
     *
     * Авторизация пользователя в системе
     *
     * @param Request $request
     *
     * @return Response
     */
    public function login(LoginRequest $request): Response
    {
        $data = $request->validated();
        $user = User::getByName($data['name']);

        try {
            $api_token = Str::random(60);
            $user->update(['api_token' => $api_token]);
        } catch (\Exception $e) {
            $message = ['message' => 'Не удалось авторизовать пользователя'];

            return new Response($message, 500);
        }
        $message = ['message' => 'Вы были успешно авторизованы', 'token' => $user->api_token];

        return new Response($message);
    }
}
