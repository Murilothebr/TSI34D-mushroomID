<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;

class MushroomController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Mushroom Quizzes';

        $paginator = null;
        $mushrooms = [];

        if ($request->acceptJson()) {
            $this->renderJson('mushrooms/index', compact('paginator', 'mushrooms', 'title'));
        } else {
            $this->render('mushrooms/index', compact('paginator', 'mushrooms', 'title'));
        }
    }
}
