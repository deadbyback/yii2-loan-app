<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\User;
use yii\web\NotFoundHttpException;

final class UserRepository
{
    public function findById(int $id): ?User
    {
        return User::findOne($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::findOne(['email' => $email]);
    }

    public function save(User $user): bool
    {
        return $user->save();
    }

    public function getById(int $id): User
    {
        $user = $this->findById($id);

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }

        return $user;
    }

    public function deleteById(int $id): bool
    {
        $user = $this->findById($id);
        if ($user === null) {
            throw new NotFoundHttpException('Loan application not found');
        }

        $deletedCount = $user->delete();

        return is_int($deletedCount) && $deletedCount > 0;
    }
}