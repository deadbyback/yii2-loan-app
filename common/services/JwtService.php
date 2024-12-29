<?php

declare(strict_types=1);

namespace common\services;

use Yii;
use common\models\User;
use sizeg\jwt\Jwt;
use Lcobucci\JWT\Token;

final class JwtService
{
    private const TOKEN_EXPIRE_TIME = 3600; // 1 hour

    public function generateToken(User $user): string
    {
        /** @var Jwt $jwt */
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();

        $token = $jwt->getBuilder()
            ->issuedBy(Yii::$app->request->hostInfo)
            ->identifiedBy(Yii::$app->id, true)
            ->issuedAt($time)
            ->expiresAt($time + self::TOKEN_EXPIRE_TIME)
            ->withClaim('uid', $user->id)
            ->getToken($signer, $key);

        return (string)$token;
    }

    public function validateToken(string $token): bool
    {
        try {
            /** @var Jwt $jwt */
            $jwt = Yii::$app->jwt;
            $token = $jwt->getParser()->parse($token);

            return $token->verify(new \Lcobucci\JWT\Signer\Hmac\Sha256(), $jwt->key) &&
                !$token->isExpired(new \DateTimeImmutable());
        } catch (\Exception $e) {
            return false;
        }
    }
}