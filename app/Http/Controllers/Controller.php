<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    public function json(mixed $data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }
}
