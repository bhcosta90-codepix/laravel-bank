<?php

namespace App\Exceptions;

use Costa\Entity\Exceptions\EntityException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
//        if ($e instanceof BadRequestException) {
//            return response()->json([
//                'message' => $e->getMessage(),
//            ], 400);
//        }
//
//        if ($e instanceof NotificationException || $e instanceof EntityException) {
//            return response()->json([
//                'message' => $e->getMessage(),
//                'errors' => [
//                    $e->getMessage(),
//                ],
//            ], Response::HTTP_UNPROCESSABLE_ENTITY);
//        }

        match (true) {
            $e instanceof EntityException => throw ValidationException::withMessages([__($e->getMessage())]),
            default => null,
        };

        return parent::render($request, $e);
    }
}
