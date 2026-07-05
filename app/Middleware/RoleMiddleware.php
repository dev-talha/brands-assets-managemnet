<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class RoleMiddleware
{
    public function handle(Request $request, array $params = []): void
    {
        $user = currentUser();
        if (!$user) {
            Response::redirect('/login');
        }

        if (!empty($params) && !in_array($user['role'], $params)) {
            Response::error(403, 'You do not have permission to access this page.');
        }
    }
}
