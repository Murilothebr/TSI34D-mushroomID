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

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $mushroom = Mushroom::findById($params['id']);

        $title = "Editar Cogumelo #{$mushroom->id}";
        $this->render('mushrooms/edit', compact('mushroom', 'title'));
    }

    public function create(Request $request): void
    {
        $params = $request->getParam('mushroom');
        $mushroom = new Mushroom($params);
        $title = 'Novo Cogumelo';
        $file = $_FILES['image_file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK || !$this->validateAndUploadImage($file, $mushroom, $title)) {
            $this->render('mushrooms/new', compact('mushroom', 'title'));
            return;
        }

        if ($mushroom->save()) {
            FlashMessage::success('Cogumelo registrado com sucesso!');
            $this->redirectTo(route('mushrooms.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $title = 'Novo Cogumelo';
            $this->render('mushrooms/new', compact('mushroom', 'title'));
        }
    }

    public function update(Request $request): void
    {
        $id = $request->getParam('id');
        $params = $request->getParam('mushroom');
        $mushroom = Mushroom::findById($id);

        if (!$mushroom) {
            FlashMessage::danger('Cogumelo não encontrado.');
            $this->redirectTo(route('mushrooms.index'));
            return;
        }

        $mushroom->scientific_name = $params['scientific_name'];
        $mushroom->hint = $params['hint'];
        $mushroom->description = $params['description'];

        $title = "Editar Cogumelo #{$mushroom->id}";
        $file = $_FILES['image_file'] ?? null;

        if ($file && $file['error'] === UPLOAD_ERR_OK && !$this->validateAndUploadImage($file, $mushroom, $title)) {
            $this->render('mushrooms/edit', compact('mushroom', 'title'));
            return;
        }

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
            return;
        }

        try {
            if (!empty($mushroom->image_url)) {
                $imagePath = __DIR__ . '/../../public' . $mushroom->image_url;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $mushroom->destroy();
            FlashMessage::success('Cogumelo removido com sucesso!');
        } catch (Exception $e) {
            FlashMessage::danger('Não é possível remover este cogumelo, pois ele está vinculado a um ou mais quizzes.');
        }

        $this->redirectTo(route('mushrooms.index'));
    }

    private function validateAndUploadImage($file, &$mushroom, string $title): bool
    {
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if ($file['size'] > $maxFileSize) {
            FlashMessage::danger('A imagem excede o tamanho máximo permitido de 2MB.');
            return false;
        }

        $imageInfo = getimagesize($file['tmp_name']);

        if (!$imageInfo) {
            FlashMessage::danger('O arquivo enviado não é uma imagem válida.');
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png'];
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        $detectedMimeType = $imageInfo['mime'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($detectedMimeType, $allowedTypes) || !in_array($extension, $allowedExtensions)) {
            FlashMessage::danger('Formato de imagem inválido. Utilize JPEG ou PNG.');
            return false;
        }

        $uploadDir = __DIR__ . '/../../public/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            if (!empty($mushroom->image_url)) {
                $oldImagePath = __DIR__ . '/../../public' . $mushroom->image_url;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $mushroom->image_url = '/uploads/' . $filename;
            return true;
        }

        FlashMessage::danger('Erro ao salvar a imagem. Tente novamente.');
        return false;
    }
}
