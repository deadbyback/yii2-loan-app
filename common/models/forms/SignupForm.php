<?php

declare(strict_types=1);

namespace common\models\forms;

use common\models\User;
use yii\base\Model;

class SignupForm extends Model
{
    public ?string $email = null;
    public ?string $password = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $date_of_birth = null;
    public ?string $passport_number = null;
    public ?string $passport_expiry_date = null;

    public function rules(): array
    {
        return [
            [['email', 'password', 'first_name', 'last_name', 'date_of_birth', 'passport_number', 'passport_expiry_date'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class],
            ['password', 'string', 'min' => 8],
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['date_of_birth', 'passport_expiry_date'], 'date', 'format' => 'php:Y-m-d'],
            ['passport_number', 'unique', 'targetClass' => User::class],
        ];
    }

    public function signup(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->email = $this->email;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->date_of_birth = $this->date_of_birth;
        $user->passport_number = $this->passport_number;
        $user->passport_expiry_date = $this->passport_expiry_date;
        $user->setPassword($this->password);

        return $user->save() ? $user : null;
    }
}