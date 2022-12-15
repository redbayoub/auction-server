<?php

namespace App\Exceptions;

use App\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->wantsJson()) {
                if ($e instanceof AuthenticationException)
                    return JsonResponse::fail('Unauthenticated', null, Response::HTTP_UNAUTHORIZED);

                if ($e instanceof ValidationException)
                    return JsonResponse::fail($e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

                if ($e instanceof ModelNotFoundException)
                    return JsonResponse::fail('Resource Not found', null, Response::HTTP_NOT_FOUND);

                return $this->renderUnhandledException($e, $request);
            }
            return parent::render($request, $e);
        });
    }


    private function renderUnhandledException(Throwable $e, Request $request)
    {
        $status = 400;
        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();
        }

        $isAppInDebugMode = config('app.debug');

        // Define the response
        $defaultResponse = [
            'message' => $status >= 500 && $isAppInDebugMode ?
                'Sorry, something went wrong.' : $e->getMessage(),
            'data' => null,
        ];

        if ($isAppInDebugMode) {
            $defaultResponse['data'] = [
                'exception' => get_class($e),
                'trace' => $e->getTrace(),
            ];
        }
        return JsonResponse::fail($defaultResponse['message'], $defaultResponse['data'], $status);
    }
}
