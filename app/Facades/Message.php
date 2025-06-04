<?php

namespace App\Facades;

use Illuminate\Http\JsonResponse;

class Message extends JsonResponse
{
    public static function render(
        $code = self::HTTP_OK,
        $message = "",
        $data = null,
        $pagination = null
    ): JsonResponse {
        $response = [
            "code" => $code,
            "message" => $message
        ];

        if ($data || (is_array($data))) {
            $response["data"] = $data;
            // $response["data"] = Helper::encryptApiResponse($data);
        }

        if ($pagination) {
            $response["pagination"] = $pagination;
        }

        return response()->json($response, $code);
    }

    public static function success(
        $message = "",
        $data = null
    ): JsonResponse {
        return self::render(
            code: self::HTTP_OK,
            message: $message,
            data: $data
        );
    }

    public static function paginate(
        $message = "",
        $data
    ): JsonResponse {
        return self::render(
            code: self::HTTP_OK,
            message: $message,
            data: $data->items(),
            pagination: [
                "current_page" => $data->currentPage(),
                "last_page" => $data->lastPage(),
                "total" => $data->total(),
                "from" => $data->firstItem(),
                "to" => $data->lastItem(),
            ]
        );
    }
    public static function create(
        $message = "Data has been created!",
        $data = null
    ): JsonResponse {
        return self::render(
            code: self::HTTP_CREATED,
            message: $message,
            data: $data
        );
    }

    public static function unauhtorize(
        $message = "Unauthorized!"
    ): JsonResponse {
        return self::render(
            code: self::HTTP_UNAUTHORIZED,
            message: $message
        );
    }

    public static function warning(
        $message = ""
    ): JsonResponse {
        return self::render(
            code: self::HTTP_BAD_REQUEST,
            message: $message
        );
    }

    public static function notFound(
        $message = "Data not found!"
    ): JsonResponse {
        return self::render(
            code: self::HTTP_NOT_FOUND,
            message: $message
        );
    }

    public static function validator(
        $message = "Fill data correctly!",
        $data = [],
        $isList = false
    ): JsonResponse {
        if ($isList && count($data) > 1) {
            $message .= ' and ' . (count($data) - 1) . ' other errors.';
        }

        return self::render(
            code: self::HTTP_UNPROCESSABLE_ENTITY,
            message: $message,
            data: $data
        );
    }

    public static function error(
        $message = "Something went Wrong!"
    ): JsonResponse {
        return self::render(
            code: self::HTTP_INTERNAL_SERVER_ERROR,
            message: $message
        );
    }

    public static function forbidden(
        $message = "Forbidden!"
    ): JsonResponse {
        return self::render(
            code: self::HTTP_FORBIDDEN,
            message: $message
        );
    }
}
