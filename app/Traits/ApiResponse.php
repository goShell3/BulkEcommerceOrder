<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function successResponse($data, $message = null, $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message, $code, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    protected function showOne(JsonResource $resource, $message = null, $code = 200): JsonResponse
    {
        return $this->successResponse($resource, $message, $code);
    }

    protected function showAll(ResourceCollection $collection, $message = null, $code = 200): JsonResponse
    {
        if ($collection->resource instanceof LengthAwarePaginator) {
            return $this->successResponse([
                'data' => $collection,
                'pagination' => [
                    'total' => $collection->total(),
                    'per_page' => $collection->perPage(),
                    'current_page' => $collection->currentPage(),
                    'last_page' => $collection->lastPage(),
                    'from' => $collection->firstItem(),
                    'to' => $collection->lastItem()
                ]
            ], $message, $code);
        }

        return $this->successResponse($collection, $message, $code);
    }

    protected function showMessage($message, $code = 200): JsonResponse
    {
        return $this->successResponse(null, $message, $code);
    }
} 