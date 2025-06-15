<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;
use App\Models\QuizMushroom;
use App\Models\Mushroom;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $created_at
 */
class Quiz extends Model
{
    protected static string $table = 'quizzes';
    protected static array $columns = ['id', 'name', 'description', 'created_at'];

    public array $mushroom_ids = [];

    public function validates(): void
    {
        Validations::notEmpty('name', $this);
        Validations::uniqueness('name', $this);
    }

    /**
     * Retorna os registros de ligação entre o quiz e os cogumelos.
     *
     * @return QuizMushroom[]
     */
    public function quizMushrooms(): array
    {
        return QuizMushroom::where(['quiz_id' => $this->id]);
    }

    /**
     * Retorna os cogumelos associados a este quiz.
     *
     * @return Mushroom[]
     */
    public function mushrooms(): array
    {
        $links = $this->quizMushrooms();
        $mushroomIds = array_map(fn($link) => $link->mushroom_id, $links);
        return Mushroom::whereIn('id', $mushroomIds);
    }
}
