<?php

namespace Modules\Core\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function render($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            if (isset($exception->status)) {
                $status = $exception->status;
            } else {
                $status = $this->isHttpException($exception) ? $exception->getStatusCode() : 500;
            }
                return new JsonResponse(
                    $this->convertExceptionToArray($exception),
                    $status,
                    $this->isHttpException($exception) ? $exception->getHeaders() : [],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                );
        }

        return parent::render($request, $exception);
    }

    protected function convertExceptionToArray(Exception $e)
    {
        $message = $e->getMessage();
        if ($e instanceof ValidationException) {
            $key = array_keys($e->errors())[0];
            $message = $e->errors()[$key][0];
        }
        if ($e instanceof UnauthorizedHttpException) {
            $message = 'Unauthorized';
        }

        $errorAttributes = config('app.debug') ? [
            'message' => $message ?? $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
                'message' => $this->isHttpException($e) ?
                    (!empty($e->getMessage()) ? $e->getMessage() : $message) : 'Server Error',
        ];


        return [
            'data' => [
                'id' => uniqid(),
                'type' => 'errors',
                'attributes' => $errorAttributes,
            ]
        ];
    }
}
