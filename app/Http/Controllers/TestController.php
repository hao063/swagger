<?php

namespace App\Http\Controllers;


use App\Http\Request\IndexRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class TestController extends Controller
{
    public function __construct()
    {
    }

   /**
    * @OA\Get(
    *     path="/api/test/{index2}",
    *     security={{
    *         "host": {},
    *         "user-agent": {},
    *         "accept": {},
    *         "accept-language": {},
    *         "accept-charset": {},
    *       }},
    *     summary="Swagger App\Http\Controllers\TestController@index",
    *     description="",
    *     tags={"Test"},
    *     @OA\Parameter(
    *         name="index2",
    *         in="path",
    *         required=true,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="classId",
    *         in="query",
    *         required=false,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="keyWork",
    *         in="query",
    *         required=false,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Return information",
    *         @OA\JsonContent(
    *             type="object",
    *            @OA\Property(property="name", type="integer"),
    *            @OA\Property(property="email", type="integer"),
    *            @OA\Property(
    *                property="classes",
    *                type="array",
    *                @OA\Items(
    *                    @OA\Property(property="id", type="integer"),
    *                    @OA\Property(property="name", type="string"),
    *                )
    *            ),
    *         )
    *     )
    * )
    */
    public function index(IndexRequest $request): JsonResponse
    {
        return Response::json([
            'name'    => 123,
            'email'   => 123,
            'classes' => [
                [
                    'id'   => 1,
                    'name' => 'lớp 1'
                ],
                [
                    'id'   => 2,
                    'name' => 'lớp 2'
                ]
            ]
        ]);
    }

   /**
    * @OA\Get(
    *     path="/api/test-2",
    *     security={{
    *         "host": {},
    *         "user-agent": {},
    *         "accept": {},
    *         "accept-language": {},
    *         "accept-charset": {},
    *       }},
    *     summary="Swagger App\Http\Controllers\TestController@index2",
    *     description="",
    *     tags={"Test"},
    *     @OA\Response(
    *         response=200,
    *         description="Return information",
    *         @OA\JsonContent(
    *             type="object",
    *            @OA\Property(property="phone", type="string"),
    *            @OA\Property(property="phone2", type="string"),
    *         )
    *     )
    * )
    */
    public function index2(): JsonResponse
    {
        return Response::json([
            'phone'  => '123',
            'phone2' => '123',
        ]);
    }

   /**
    * @OA\Get(
    *     path="/api/test-3",
    *     security={{
    *         "host": {},
    *         "user-agent": {},
    *         "accept": {},
    *         "accept-language": {},
    *         "accept-charset": {},
    *       }},
    *     summary="Swagger App\Http\Controllers\TestController@index3",
    *     description="",
    *     tags={"Test"},
    *     @OA\Response(
    *         response=200,
    *         description="Return information",
    *         @OA\JsonContent(
    *             type="object",
    *            @OA\Property(property="phone", type="string"),
    *         )
    *     )
    * )
    */
    public function index3(): JsonResponse
    {
        return Response::json([
            'phone' => '123',
        ]);
    }

}

