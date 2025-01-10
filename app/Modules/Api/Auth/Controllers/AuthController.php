<?php

namespace App\Modules\Api\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Api\Auth\Requests\LoginRequest;
use App\Modules\Api\Auth\Requests\RegisterRequest;
use App\Modules\Api\Auth\Resources\RegisterResource;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Авторизация/Базовые методы",
 *         version="1.0.0",
 *     )
 * )
 *
 * Class Controller
 * @package App\Modules\Api\Auth\Controllers
 */
class AuthController extends Controller
{
    /**
     * Регистрация пользователя в системе.
     *
     * @OA\Post(
     *      path="/api/register",
     *      summary="Регистрация пользователя",
     *      description="Для регистрации нужен email и пароль пользователя",
     *      operationId="authRegister",
     *      tags={"Авторизация"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Значения общих настроек",
     *          @OA\JsonContent(
     *              @OA\Property(property="first_name", description="First Name", type="string", example="First Name"),
     *              @OA\Property(property="last_name", description="Last Name", type="string", example="Last Name"),
     *              @OA\Property(property="email", description="Email", type="string", example="admin@admin.ru"),
     *              @OA\Property(property="password", description="Пароль", type="string", example="password123"),
     *              @OA\Property(property="password_confirmation", description="Подтверждение пароля", type="string", example="password123")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неверные авторизационные данные",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="error_code", type="integer", example="2"),
     *                  @OA\Property(property="error_msg", type="string", example="Неверные авторизационные данные.")
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="error_code", type="integer", example="1"),
     *                  @OA\Property(property="error_msg", type="string", example="Ошибка валидации."),
     *                  @OA\Property(
     *                      property="fields",
     *                      type="object",
     *                      @OA\Property(property="email", type="array", @OA\Items(example="Поле email должно содержать корректный email.")),
     *                  )
     *              )
     *          )
     *      )
     * )
     *
     * @param  RegisterRequest  $request
     * @return RegisterResource
     */
    public function register(RegisterRequest $request): RegisterResource
    {
        $user = User::create($request->validated());

        return RegisterResource::make($user);
    }

    /**
     * Авторизация пользователя в системе.
     *
     * @OA\Post(
     *      path="/api/login",
     *      summary="Авторизация пользователя",
     *      description="Для авторизации нужен email и пароль пользователя",
     *      operationId="authLogin",
     *      tags={"Авторизация"},
     *      @OA\RequestBody(
     *          required=true,
     *          description="Значения общих настроек",
     *          @OA\JsonContent(
     *              @OA\Property(property="email", description="Email", type="string", example="admin@admin.ru"),
     *              @OA\Property(property="password", description="Пароль", type="string", example="password123")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Неверные авторизационные данные",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="error_code", type="integer", example="2"),
     *                  @OA\Property(property="error_msg", type="string", example="Неверные авторизационные данные.")
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Ошибка валидации",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="error",
     *                  type="object",
     *                  @OA\Property(property="error_code", type="integer", example="1"),
     *                  @OA\Property(property="error_msg", type="string", example="Ошибка валидации."),
     *                  @OA\Property(
     *                      property="fields",
     *                      type="object",
     *                      @OA\Property(property="email", type="array", @OA\Items(example="Поле email должно содержать корректный email.")),
     *                  )
     *              )
     *          )
     *      )
     * )
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'error' => [
                    'error_code' => 2,
                    'error_msg' => __('auth.invalid_credentials'),
                ],
            ], 401);
        }

        return $this->createToken();
    }

    /**
     *
     * Авторизация пользователя в мобильном приложении.
     *
     * @OA\Get(
     *      path="/api/logout",
     *      summary="Разлогинивание пользователя",
     *      description="Разлогинивает пользователя",
     *      operationId="authLogout",
     *      tags={"Авторизация"},
     *      security={ {"sanctum": {} }},
     *      @OA\Response(
     *          response=204,
     *          description="Разлогинивание прошло успешно",
     *      )
     * )
     *
     * @param Request $request
     * @return ResponseFactory|Application|Response
     */
    public function logout(Request $request): Application|Response|ResponseFactory
    {
        $tokenAr = explode(" ", $request->header('Authorization'));
        $token = PersonalAccessToken::findToken($tokenAr[1]);
        $token->delete();
        $request->session()->invalidate();

        return response(null, ResponseAlias::HTTP_NO_CONTENT);
    }

    /**
     * Создание токена для авторизованного пользователя.
     *
     * @return JsonResponse
     */
    private function createToken(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $newToken = $user->createToken('auth_token_mobile')->plainTextToken;

        /** @var PersonalAccessToken $accessToken */
        $accessToken = $user->tokens()->where('name', 'accessToken')->first();
        if (empty($accessToken)) {
            $user->createToken('accessToken');
        }

        return response()->json([
            'data' => [
                'token' => $newToken,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ]
            ],
        ]);
    }
}
