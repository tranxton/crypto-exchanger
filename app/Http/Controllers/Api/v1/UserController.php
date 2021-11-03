<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\User\GetRequest as UserGetRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest as UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/user/get",
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
            $user = UserRepository::get($id);
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
     *      description="Возвращает true",
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
     *          description="Произошла неизвестная ошибка"
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
        $validated = $request->validated();

        try {
            UserRepository::create($validated);
        } catch (\Exception $e) {
            $message = ['message' => $e->getMessage()];

            return new Response($message, 500);
        }

        $message = ['message' => 'Пользователь успешно зарегистрирован'];

        return new Response($message);
    }

    /**
     * @OA\Get(
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
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Произошла неизвестная ошибка"
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
        $user = UserRepository::getByName($data['name']);

        try {
            $user = UserRepository::login($user);
        } catch (\Exception $e) {
            $message = ['message' => $e->getMessage()];

            return new Response($message, 500);
        }
        $message = ['message' => 'Вы были успешно авторизованы', 'token' => $user->api_token];

        return new Response($message);
    }
}
