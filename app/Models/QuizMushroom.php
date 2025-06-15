<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;

/**
 * @property int $quiz_id
 * @property int $mushroom_id
 */
class QuizMushroom extends Model
{
    protected static string $table = 'quiz_mushrooms';
    protected static array $columns = ['quiz_id', 'mushroom_id'];

    public static function primaryKey(): array
    {
        return ['quiz_id', 'mushroom_id'];
    }

    /**
     * @return Quiz|null
     */
    public function quiz(): ?Quiz
    {
        return $this->belongsTo(Quiz::class, 'quiz_id')->get();
    }

    /**
     * @return Mushroom|null
     */
    public function mushroom(): ?Mushroom
    {
        return $this->belongsTo(Mushroom::class, 'mushroom_id')->get();
    }
}
