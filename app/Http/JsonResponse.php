<?php

namespace App\Http;

use Illuminate\Pagination\LengthAwarePaginator;

class JsonResponse
{
    public static function success($message, $data = null, $status = 200)
    {

        if ($data instanceof LengthAwarePaginator)
            $data = [
                "data" => $data->getCollection(),
                "pagination" => [
                    'total' => $data->total(),
                    'lastPage' => $data->lastPage(),
                    'perPage' => $data->perPage(),
                    'currentPage' => $data->currentPage(),
                    'nextPageUrl' => $data->nextPageUrl(),
                    'previousPageUrl' => $data->previousPageUrl(),
                ]
            ];

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
