<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(Request $request, array $params = []): void
    {
        if (!isLoggedIn()) {
            $_SESSION['intended_url'] = $request->uri();
            Response::redirect('/login');
        }
    }
}
