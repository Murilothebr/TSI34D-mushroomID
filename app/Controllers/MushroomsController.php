<?php

namespace App\Controllers;

use App\Models\Mushroom;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Exception;
use Lib\FlashMessage;

class MushroomsController extends Controller
{
    public function index(Request $request): void
    {
        $paginator = Mushroom::paginate(page: $request->getParam('page', 1));
        $mushrooms = $paginator->registers();

        $title = 'Cogumelos Registrados';

        if ($request->acceptJson()) {
            $this->renderJson('mushrooms/index', compact('paginator', 'mushrooms', 'title'));
        } else {
            $this->render('mushrooms/index', compact('paginator', 'mushrooms', 'title'));
        }
    }

    public function show(Request $request): void
    {
        $params = $request->getParams();
        $mushroom = Mushroom::findById($params['id']);

        $title = "";
        $this->render('mushrooms/show', compact('mushroom', 'title'));
    }

    public function new(): void
    {
        $mushroom = new Mushroom();

        $title = 'Novo Cogumelo';
        $this->render('mushrooms/new', compact('mushroom', 'title'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParam('mushroom');
        $mushroom = new Mushroom($params);

        if ($mushroom->save()) {
            FlashMessage::success('Cogumelo registrado com sucesso!');
            $this->redirectTo(route('mushrooms.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $title = 'Novo Cogumelo';
            $this->render('mushrooms/new', compact('mushroom', 'title'));
        }
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $mushroom = Mushroom::findById($params['id']);

        $title = "Editar Cogumelo #{$mushroom->id}";
        $this->render('mushrooms/edit', compact('mushroom', 'title'));
    }

    public function update(Request $request): void
    {
        $id = $request->getParam('id');
        $params = $request->getParam('mushroom');

        $mushroom = Mushroom::findById($id);
        $mushroom->scientific_name = $params['scientific_name'];
        $mushroom->image_url = $params['image_url'];
        $mushroom->hint = $params['hint'];
        $mushroom->description = $params['description'];

        if ($mushroom->save()) {
            FlashMessage::success('Cogumelo atualizado com sucesso!');
            $this->redirectTo(route('mushrooms.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $title = "Editar Cogumelo #{$mushroom->id}";
            $this->render('mushrooms/edit', compact('mushroom', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $id = $request->getParam('id');
        $mushroom = Mushroom::findById($id);

        if (!$mushroom) {
            FlashMessage::danger('Cogumelo não encontrado.');
            $this->redirectTo(route('mushrooms.index'));
        }

        try {
            $mushroom->destroy();
            FlashMessage::success('Cogumelo removido com sucesso!');
        } catch (Exception $e) {
            FlashMessage::danger('Não é possível remover este cogumelo, pois ele está vinculado a um ou mais quizzes.');
        }

        $this->redirectTo(route('mushrooms.index'));
    }
}
