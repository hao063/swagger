<?php

namespace App\Http\Controllers;


use App\Services\TestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class TestController extends Controller
{
    public function __construct(
        private TestService $testService
    ) {
    }



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
    public function index(): JsonResponse
    {
        $user = $this->testService->execute();
        return Response::json([
            'name'  => $user->username,
            'email' => $user->email,
        ]);
    }

}