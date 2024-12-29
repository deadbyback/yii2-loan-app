<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\Document;
use common\services\DocumentService;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

final class DocumentController extends AdminController
{
    public function __construct(
        $id,
        $module,
        private readonly DocumentService $documentService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionDownload(int $id): Response
    {
        $document = $this->documentService->getDocumentById($id);
        $filePath = Yii::getAlias('@frontend/web/uploads/') . $document->file_path;

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('File not found');
        }

        return Yii::$app->response->sendFile(
            $filePath,
            $document->original_name,
            ['inline' => false]
        );
    }

    public function actionIndex(): string
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => Document::find()->with('user'),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDelete(int $id): Response
    {
        try {
            $this->documentService->deleteDocument($id);
            Yii::$app->session->setFlash('success', 'Document has been deleted.');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Error deleting document.');
        }

        return $this->redirect(['index']);
    }
}