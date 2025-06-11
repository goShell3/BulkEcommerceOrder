<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    use ApiResponse;

    /**
     * Success response method.
     *
     * @param  mixed  $result
     * @param  string $message
     * @return JsonResponse
     */
    public function sendResponse($result, string $message): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * Error response method.
     *
     * @param  string $error
     * @param  array  $errorMessages
     * @param  int    $code
     * @return JsonResponse
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }


    protected function respondWithError(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return $this->errorResponse($message, $code, $errors);
    }

    protected function respondWithSuccess($data = null, string $message = null, int $code = 200): JsonResponse
    {
        return $this->successResponse($data, $message, $code);
    }

    protected function respondWithMessage(string $message, int $code = 200): JsonResponse
    {
        return $this->showMessage($message, $code);
    }
}

