<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ApiResponse implements Responsable
{
    public function __construct(
        protected bool $success,
        protected string $message,
        protected mixed $data = null,
        protected int $status = 200
    ) {}

    public static function success(string $message, mixed $data = null, int $status = 200): self
    {
        return new self(true, $message, $data, $status);
    }

    public static function error(string $message, mixed $data = null, int $status = 400): self
    {
        return new self(false, $message, $data, $status);
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
        ], $this->status);
    }
}
