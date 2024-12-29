<?php

declare(strict_types=1);

namespace frontend\controllers;

use common\models\forms\DocumentUploadForm;
use common\services\DocumentService;
use OpenApi\Annotations as OA;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * @OA\Tag(
 *     name="Documents",
 *     description="Document management"
 * )
 *
 * @OA\Schema(
 *     schema="Document",
 *     required={"id", "user_id", "type", "file_path", "original_name", "mime_type", "size"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="type", type="string", enum={"passport", "income"}),
 *     @OA\Property(property="file_path", type="string"),
 *     @OA\Property(property="original_name", type="string"),
 *     @OA\Property(property="mime_type", type="string"),
 *     @OA\Property(property="size", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="datetime")
 * )
 */
final class DocumentController extends ApiController
{
    public function __construct(
        $id,
        $module,
        private readonly DocumentService $documentService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * @OA\Post(
     *     path="/document/upload",
     *     summary="Upload document",
     *     tags={"Documents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file", "type"},
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Property(property="type", type="string", enum={"passport", "income"})
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="original_name", type="string"),
     *             @OA\Property(property="mime_type", type="string"),
     *             @OA\Property(property="size", type="integer"),
     *             @OA\Property(property="created_at", type="string", format="datetime")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input data"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function actionUpload(): array
    {
        $this->checkPermission('uploadDocuments');

        $form = new DocumentUploadForm();
        $form->file = UploadedFile::getInstanceByName('file');
        $form->type = Yii::$app->request->post('type');

        if (!$form->validate()) {
            throw new BadRequestHttpException('Invalid document data');
        }

        return $this->documentService->uploadDocument(
            $form->file,
            $form->type,
            Yii::$app->user->getId()
        )->getAttributes();
    }

    /**
     * @OA\Get(
     *     path="/document/list",
     *     summary="Get user documents",
     *     tags={"Documents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of documents",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="type", type="string"),
     *                 @OA\Property(property="original_name", type="string"),
     *                 @OA\Property(property="mime_type", type="string"),
     *                 @OA\Property(property="size", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function actionList(): array
    {
        $this->checkPermission('uploadDocuments');

        if (Yii::$app->user->can('manageUsers')) {
            return array_map(
                fn($document) => $document->getAttributes(),
                $this->documentService->getAllDocuments()
            );
        }

        return array_map(
            fn($document) => $document->getAttributes(),
            $this->documentService->getDocumentsByUserId(Yii::$app->user->getId())
        );
    }
}