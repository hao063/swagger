<?php

/**
 * @OA\Get(
 *     path="api/test",
 *     summary="Lấy thông tin người dùng",
 *     description="Trả về thông tin cơ bản của người dùng.",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Thông tin người dùng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", example="haonp"),
@OA\Property(property="email", type="string", example="admin@123.com")
 *         )
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="api/test-2",
 *     summary="Lấy thông tin người dùng",
 *     description="Trả về thông tin cơ bản của người dùng.",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Thông tin người dùng",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="string", example="123")
 *         )
 *     )
 * )
 */
