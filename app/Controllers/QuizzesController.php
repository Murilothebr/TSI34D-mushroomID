<?php

namespace App\Controllers;

use App\Models\Quiz;
use App\Models\Mushroom;
use App\Models\QuizMushroom;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class QuizzesController extends Controller
{
    public function index(Request $request): void
    {
        $paginator = Quiz::paginate(page: $request->getParam('page', 1));
        $quizzes = $paginator->registers();

        $title = 'Quizzes Registrados';

        if ($request->acceptJson()) {
            $this->renderJson('quizzes/index', compact('paginator', 'quizzes', 'title'));
        } else {
            $this->render('quizzes/index', compact('paginator', 'quizzes', 'title'));
        }
    }

    public function show(Request $request): void
    {
        $params = $request->getParams();
        $quiz = Quiz::findById($params['id']);

        $title = "Quiz #{$quiz->id}";
        $this->render('quizzes/show', compact('quiz', 'title'));
    }

    public function new(): void
    {
        $quiz = new Quiz();
        $quiz->mushroom_ids = [];
        $allMushrooms = Mushroom::all();
        $title = 'Novo Quiz';

        $this->render('quizzes/new', compact('quiz', 'allMushrooms', 'title'));
    }

    public function create(Request $request): void
    {
        $data = $request->getParam('quiz');
        $quiz = new Quiz($data);
        $quiz->created_at = date('Y-m-d H:i:s');

        if ($quiz->save()) {
            $this->syncMushrooms($quiz, $data['mushroom_ids'] ?? []);
            FlashMessage::success('Quiz criado com sucesso!');
            $this->redirectTo(route('quizzes.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $allMushrooms = Mushroom::all();
            $title = 'Novo Quiz';
            $quiz->mushroom_ids = $data['mushroom_ids'] ?? [];
            $this->render('quizzes/new', compact('quiz', 'allMushrooms', 'title'));
        }
    }

    public function edit(Request $request): void
    {
        $params = $request->getParams();
        $quiz = Quiz::findById($params['id']);
        $quiz->mushroom_ids = array_map(fn($m) => $m->id, $quiz->mushrooms());

        $allMushrooms = Mushroom::all();
        $title = "Editar Quiz #{$quiz->id}";
        $this->render('quizzes/edit', compact('quiz', 'allMushrooms', 'title'));
    }

    public function update(Request $request): void
    {
        $id = $request->getParam('id');
        $data = $request->getParam('quiz');

        $quiz = Quiz::findById($id);
        $quiz->name = $data['name'];
        $quiz->description = $data['description'];

        if ($quiz->save()) {
            $this->syncMushrooms($quiz, $data['mushroom_ids'] ?? []);
            FlashMessage::success('Quiz atualizado com sucesso!');
            $this->redirectTo(route('quizzes.index'));
        } else {
            FlashMessage::danger('Existem dados incorretos! Por favor, verifique!');
            $quiz->mushroom_ids = $data['mushroom_ids'] ?? [];
            $allMushrooms = Mushroom::all();
            $title = "Editar Quiz #{$quiz->id}";
            $this->render('quizzes/edit', compact('quiz', 'allMushrooms', 'title'));
        }
    }

    public function destroy(Request $request): void
    {
        $params = $request->getParams();
        $quiz = Quiz::findById($params['id']);

        QuizMushroom::deleteWhere(['quiz_id' => $quiz->id]);
        $quiz->destroy();

        FlashMessage::success('Quiz removido com sucesso!');
        $this->redirectTo(route('quizzes.index'));
    }

    /**
     * Sincroniza os registros da tabela quiz_mushrooms
     *
     * @param Quiz $quiz
     * @param array<int> $mushroomIds
     */
    private function syncMushrooms(Quiz $quiz, array $mushroomIds): void
    {
        // Remove os existentes
        QuizMushroom::deleteWhere(['quiz_id' => $quiz->id]);

        // Cria os novos
        foreach ($mushroomIds as $mushroomId) {
            $link = new QuizMushroom([
                'quiz_id' => $quiz->id,
                'mushroom_id' => (int)$mushroomId
            ]);
            $link->save();
        }
    }
}
