<?php

declare(strict_types=1);

namespace common\services;

use common\models\Document;
use common\repositories\DocumentRepository;
use RuntimeException;
use yii\web\UploadedFile;

final class DocumentService
{
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private ?string $uploadPath
    ) {
        if ($this->uploadPath === null) {
            $this->uploadPath = \Yii::getAlias('@frontend/web/uploads/');
        }
    }

    public function uploadDocument(UploadedFile $file, string $type, int $userId): Document
    {
        $fileName = $this->generateUniqueFileName($file);
        $filePath = $this->uploadPath . '/' . $fileName;

        if (!$file->saveAs($filePath)) {
            throw new RuntimeException('Failed to save file');
        }

        $document = new Document();
        $document->user_id = $userId;
        $document->type = $type;
        $document->file_path = $fileName;
        $document->original_name = $file->name;
        $document->mime_type = $file->type;
        $document->size = $file->size;

        if (!$this->documentRepository->save($document)) {
            unlink($filePath);
            throw new RuntimeException('Failed to save document record');
        }

        return $document;
    }

    private function generateUniqueFileName(UploadedFile $file): string
    {
        return uniqid('doc_', true) . '.' . $file->extension;
    }

    public function getDocumentsByUserId(int|null $getId): array
    {
        return Document::findAll(['user_id' => $getId]);
    }

    public function getAllDocuments(): array
    {
        return $this->documentRepository->findAll();
    }

    public function deleteDocument(int $id): bool
    {
        return $this->documentRepository->deleteById($id);
    }
}