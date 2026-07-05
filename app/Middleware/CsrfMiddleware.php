<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware
{
    public function handle(Request $request, array $params = []): void
    {
        if ($request->method() === 'POST') {
            if (!verifyCsrfToken()) {
                Response::error(403, 'Invalid CSRF token. Please refresh and try again.');
            }
        }
    }
}
