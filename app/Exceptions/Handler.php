<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Services\Api\ApiReturn;

class Handler extends ExceptionHandler
{
    use ApiReturn;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
    public function register(): void
    {
        $this->renderable(function (ParamsException $e) {
            return response()->json($this->paramsErrorResult($e), 400);
        });
        $this->renderable(function (HandleException $e) {
            return response()->json($this->handleErrorResult($e), 400);
        });
        $this->renderable(function (AccessTokenException $e) {
            return response()->json($this->accessTokenErrorResult($e), 400);
        });
        $this->renderable(function (NoTargetException $e) {
            return response()->json($this->noTargetErrorResult($e), 400);
        });
    }

}
