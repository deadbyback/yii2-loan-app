<?php

namespace common\models\forms;

use common\models\Document;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class DocumentUploadForm extends Model
{
    public ?UploadedFile $file = null;
    public ?string $type = null;

    public function rules(): array
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false,
                'extensions' => ['pdf', 'jpg', 'jpeg', 'png'],
                'maxSize' => 5 * 1024 * 1024],
            ['type', 'required'],
            ['type', 'in', 'range' => [Document::TYPE_PASSPORT, Document::TYPE_INCOME]],
        ];
    }

    public function upload(int $userId): ?Document
    {
        if (!$this->validate()) {
            return null;
        }

        $document = new Document();
        $document->user_id = $userId;
        $document->type = $this->type;
        $document->original_name = $this->file->name;
        $document->mime_type = $this->file->type;
        $document->size = $this->file->size;

        $fileName = $this->generateUniqueFileName();
        $document->file_path = 'uploads/' . $fileName;

        if ($this->saveFile($fileName)) {
            return $document->save() ? $document : null;
        }

        return null;
    }

    private function generateUniqueFileName(): string
    {
        return Yii::$app->security->generateRandomString(32) . '.' . $this->file->extension;
    }

    private function saveFile(string $fileName): bool
    {
        $path = Yii::getAlias('@frontend/web/uploads/') . $fileName;
        return $this->file->saveAs($path);
    }
}