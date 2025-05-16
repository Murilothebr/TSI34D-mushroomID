<?php

namespace App\Middleware;

use Core\Http\Request;
use Lib\Authentication\Auth;
use Core\Http\Middleware\Middleware;


class AdminMiddleware implements Middleware
{
    public function handle(Request $request): void
    {
        $user = Auth::user();

        if (!$user) {
            header('Location: /login');
            exit;
        }

        if (!$user->is_admin) {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>Você não tem permissão para acessar esta página.</p>";
            exit;
        }
    }
}
