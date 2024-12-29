<?php

declare(strict_types=1);

namespace common\services;

use common\models\User;
use common\repositories\DocumentRepository;
use common\repositories\LoanApplicationRepository;
use common\repositories\UserRepository;
use RuntimeException;

final class UserService
{
    public function __construct(
        private readonly UserRepository            $userRepository,
        private readonly LoanApplicationRepository $loanRepository,
        private readonly DocumentRepository        $documentRepository
    )
    {
    }

    public function createUser(array $userData): User
    {
        $user = new User();
        $user->setAttributes($userData);

        if (!$this->userRepository->save($user)) {
            throw new RuntimeException('Failed to create user');
        }

        return $user;
    }

    public function updateUser(int $id, array $userData): User
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new RuntimeException('User not found');
        }

        $user->setAttributes($userData);

        if (!$this->userRepository->save($user)) {
            throw new RuntimeException('Failed to update user');
        }

        return $user;
    }

    public function getUserById(int $id): User
    {
        return $this->userRepository->getById($id);
    }

    public function getUserLoans(int $userId): array
    {
        return $this->loanRepository->findByUserId($userId);
    }

    public function getUserDocuments(int $userId): array
    {
        return $this->documentRepository->findByUserId($userId);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->deleteById($id);
    }
}