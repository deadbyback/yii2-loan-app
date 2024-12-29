<?php

declare(strict_types=1);

namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    public ?string $email = null;
    public ?string $password = null;
    public bool $rememberMe = true;

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect email or password.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user && $user->email === 'admin@example.com') {
                return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
            }
        }

        return false;
    }

    protected function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }

    protected function isAdmin(): bool
    {
        return Yii::$app->authManager->checkAccess($this->getUser()->getId(), 'admin');
    }
}