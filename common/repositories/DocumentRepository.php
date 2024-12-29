<?php

declare(strict_types=1);

namespace common\repositories;

use common\models\Document;
use yii\web\NotFoundHttpException;

final class DocumentRepository
{
    public function findById(int $id): ?Document
    {
        return Document::findOne($id);
    }

    public function save(Document $document): bool
    {
        return $document->save();
    }

    public function getById(int $id): Document
    {
        $document = $this->findById($id);

        if ($document === null) {
            throw new NotFoundHttpException('Document not found');
        }

        return $document;
    }

    public function findByUserId(int $userId, ?string $type = null): array
    {
        $query = Document::find()->where(['user_id' => $userId]);

        if ($type !== null) {
            $query->andWhere(['type' => $type]);
        }

        return $query->all();
    }

    public function findAll(): array
    {
        return Document::find()->all();
    }

    public function deleteById(int $id): bool
    {
        $document = $this->findById($id);

        if ($document === null) {
            throw new NotFoundHttpException('Document not found');
        }

        $deletedCount = $document->delete();

        return is_int($deletedCount) && $deletedCount > 0;
    }
}