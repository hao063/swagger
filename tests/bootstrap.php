<?php


use Illuminate\Testing\TestResponse;

// Thêm một macro trực tiếp cho assertJsonStructure
$originalAssertJsonStructure = TestResponse::class.'::assertJsonStructure';
TestResponse::macro('assertJsonStructure',
    function ($structure, $responseData = null) use ($originalAssertJsonStructure) {
        dd(123);
        // Thực hiện assertion gốc
        return call_user_func($originalAssertJsonStructure, $this, $structure, $responseData);
    });