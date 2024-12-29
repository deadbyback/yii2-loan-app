<?php

/** @var yii\web\View $this */

use yii\bootstrap5\Html;

$this->title = 'Admin Dashboard';
?>
<div class="site-index">
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-4">Welcome to Admin Panel</h1>
            <p class="fs-5 fw-light">Manage users, loan applications and documents.</p>
        </div>
    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-4">
                <h2>Users</h2>
                <p>Manage user accounts and their personal information.</p>
                <p><?= Html::a('Manage Users', ['user/index'], ['class' => 'btn btn-primary']) ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Loan Applications</h2>
                <p>Review and process loan applications.</p>
                <p><?= Html::a('Manage Loans', ['loan/index'], ['class' => 'btn btn-primary']) ?></p>
            </div>
            <div class="col-lg-4">
                <h2>Documents</h2>
                <p>Access and manage user documents.</p>
                <p><?= Html::a('Manage Documents', ['document/index'], ['class' => 'btn btn-primary']) ?></p>
            </div>
        </div>
    </div>
</div>