<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\forms\SignupForm;
use common\models\User;
use common\services\JwtService;
use OpenApi\Annotations as OA;
use Yii;
use yii\web\BadRequestHttpException;
use sizeg\jwt\Jwt;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints"
 * )
 */
final class AuthController extends ApiController
{
    public function __construct(
        $id,
        $module,
        private readonly JwtService $jwtService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * @OA\Post(
     *     path="/auth/signup",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password", "first_name", "last_name", "date_of_birth", "passport_number", "passport_expiry_date"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="first_name", type="string"),
     *             @OA\Property(property="last_name", type="string"),
     *             @OA\Property(property="date_of_birth", type="string", format="date"),
     *             @OA\Property(property="passport_number", type="string"),
     *             @OA\Property(property="passport_expiry_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="first_name", type="string"),
     *                 @OA\Property(property="last_name", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data"
     *     )
     * )
     */
    public function actionSignup(): array
    {
        $form = new SignupForm();
        if (!$form->load(Yii::$app->request->post(), '') || !$form->validate()) {
            throw new BadRequestHttpException('Invalid signup data');
        }

        $user = $form->signup();
        return [
            'token' => $this->jwtService->generateToken($user),
            'user' => $user->getAttributes(['id', 'email', 'first_name', 'last_name']),
        ];
    }

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="first_name", type="string"),
     *                 @OA\Property(property="last_name", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function actionLogin(): array
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');

        $user = User::findOne(['email' => $email]);
        if (!$user || !$user->validatePassword($password)) {
            throw new BadRequestHttpException('Invalid login credentials');
        }

        return [
            'token' => $this->jwtService->generateToken($user),
            'user' => $user->getAttributes(['id', 'email', 'first_name', 'last_name']),
        ];
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     summary="Logout user",
     *     description="In JWT, there is no real 'logout' - the client simply stops using the token",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Unauthorized"),
     *             @OA\Property(property="message", type="string", example="Your request was made with invalid credentials"),
     *             @OA\Property(property="code", type="integer", example=401),
     *             @OA\Property(property="status", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function actionLogout(): array
    {
        return [
            'success' => true,
            'message' => 'Successfully logged out'
        ];
    }
}