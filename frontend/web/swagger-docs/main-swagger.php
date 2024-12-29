<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Loan Application System API",
 *     description="API documentation for Loan Application System"
 * )
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Local API Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */