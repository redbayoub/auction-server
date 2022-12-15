<?php

namespace App\Http;

class JsonResponse
{
    public static function success($message, $data = null, $status = 200)
    {
        return response()->json([
            "status" => "success",
            "message" => $message,
            "data" => $data,
        ], $status);
    }

    public static function fail($message, $data = null, $status = 400)
    {
        return response()->json([
            "status" => "fail",
            "message" => $message,
            "data" => $data,
        ], $status);
    }
}
