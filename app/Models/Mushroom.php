<?php

namespace App\Models;

use Lib\Validations;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $scientific_name
 * @property string $image_url
 * @property string $hint
 * @property string $description
 * @property string $created_at
 */
class Mushroom extends Model
{
    protected static string $table = 'mushrooms';
    protected static array $columns = ['id', 'scientific_name', 'image_url', 'hint', 'description', 'created_at'];

    public function validates(): void
    {
        Validations::notEmpty('scientific_name', $this);
        Validations::uniqueness('scientific_name', $this);

        if (empty($this->image_url)) {
            Validations::notEmpty('image_url', $this);
        }

        Validations::notEmpty('hint', $this);
        Validations::notEmpty('description', $this);
    }

    public static function findByScientificName(string $name): Mushroom | null
    {
        return Mushroom::findBy(['scientific_name' => $name]);
    }
}
