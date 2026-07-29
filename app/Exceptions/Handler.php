<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($this->isHttpException($e)) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return parent::render($request, $e);
            }

            switch ((int) $e->getStatusCode()) {
                case 403:
                case 404:
                case 500:
                    // Same as before: go to original /admin/404 page (original layout)
                    session([
                        'error_home_url' => $this->resolveErrorHomeUrl($request),
                    ]);

                    return redirect('/admin/404');

                default:
                    return $this->renderHttpException($e);
            }
        }

        return parent::render($request, $e);
    }

    protected function resolveErrorHomeUrl($request): string
    {
        if (Auth::guard('advisor')->check()) {
            return url('/advisor/dashboard');
        }

        if (Auth::guard('web')->check() || Auth::check()) {
            return url('/admin/dashboard');
        }

        if ($request->is('advisor') || $request->is('advisor/*')) {
            return url('/advisor/login');
        }

        return url('/admin/login');
    }
}
