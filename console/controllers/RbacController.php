<?php

declare(strict_types=1);

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class RbacController extends Controller
{
    public function actionInit(): int
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // Roles
        $user = $auth->createRole('user');
        $user->description = 'Regular user';
        $auth->add($user);

        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        // Permissions
        $viewOwnProfile = $auth->createPermission('viewOwnProfile');
        $viewOwnProfile->description = 'View own profile';
        $auth->add($viewOwnProfile);

        $manageLoanApplications = $auth->createPermission('manageLoanApplications');
        $manageLoanApplications->description = 'Manage loan applications';
        $auth->add($manageLoanApplications);

        $uploadDocuments = $auth->createPermission('uploadDocuments');
        $uploadDocuments->description = 'Upload documents';
        $auth->add($uploadDocuments);

        $adminAccess = $auth->createPermission('adminAccess');
        $adminAccess->description = 'Access admin panel';
        $auth->add($adminAccess);

        // User assignments
        $auth->addChild($user, $viewOwnProfile);
        $auth->addChild($user, $manageLoanApplications);
        $auth->addChild($user, $uploadDocuments);

        // Admin assignments
        $auth->addChild($admin, $user);
        $auth->addChild($admin, $adminAccess);

        // Create default admin
        if ($this->createDefaultAdmin()) {
            $this->stdout("Default admin user created.\n");
        }

        $this->stdout("RBAC initialization completed.\n");
        return ExitCode::OK;
    }

    private function createDefaultAdmin(): bool
    {
        $user = new \common\models\User();
        $user->email = 'admin@example.com';
        $user->setPassword('admin123');
        $user->first_name = 'Admin';
        $user->last_name = 'User';
        $user->date_of_birth = '1998-01-01';
        $user->passport_number = 'ADMIN123';
        $user->passport_expiry_date = '2030-01-01';

        if ($user->save()) {
            $auth = Yii::$app->authManager;
            $adminRole = $auth->getRole('admin');
            $auth->assign($adminRole, $user->id);
            return true;
        }

        return false;
    }
}