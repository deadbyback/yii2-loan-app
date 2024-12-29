<?php

use OpenApi\Annotations as OA;

/**
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
 *
 * @OA\Schema(
 *     schema="LoanApplication",
 *     required={"id", "user_id", "amount", "term_months", "purpose", "monthly_income", "status"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="amount", type="number", format="float"),
 *     @OA\Property(property="term_months", type="integer"),
 *     @OA\Property(property="purpose", type="string"),
 *     @OA\Property(property="monthly_income", type="number", format="float"),
 *     @OA\Property(property="monthly_payment", type="number", format="float"),
 *     @OA\Property(property="status", type="string", enum={"pending", "approved", "rejected"}),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Error",
 *     @OA\Property(property="code", type="integer"),
 *     @OA\Property(property="message", type="string")
 * )
 */