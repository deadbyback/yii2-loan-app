<?php

namespace console\controllers;

use OpenApi\Annotations\OpenApi;
use OpenApi\Generator;
use yii\console\Controller;

class SwaggerController extends Controller
{
    public function actionGenerate()
    {
        $openApi = Generator::scan(
            [
                \Yii::getAlias('@frontend/controllers'),
                \Yii::getAlias('@frontend/web/swagger-docs'),
                \Yii::getAlias('@frontend/web/swagger-docs/schemas.php'),
            ],
            [
                'version' => OpenApi::VERSION_3_0_0
            ]
        );

        $outputFile = \Yii::getAlias('@frontend/web/swagger-docs/openapi.json');
        $directory = dirname($outputFile);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($outputFile, $openApi->toJson());

        $this->stdout("OpenAPI specification has been generated to {$outputFile}\n");
    }
}