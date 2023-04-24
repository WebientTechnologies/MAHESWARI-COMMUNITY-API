<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticateCustomer extends Authenticate
{
    protected function authenticate($request, array $guards)
    {
        if (! $token = JWTAuth::parseToken()->authenticate()) {
            throw new UnauthorizedHttpException('Bearer');
        }

        $this->authenticateViaBearerToken($request, $guards, $token);
    }
}
