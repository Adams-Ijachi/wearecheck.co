<?php

use App\Exceptions\ApiCustomException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ApiCustomException $apiCustomException, Request $request){
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $apiCustomException->getMessage()
                ], $apiCustomException->getCode() );
            }
        });
        //
    })->create();
