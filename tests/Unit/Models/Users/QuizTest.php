<?php

namespace Tests\Unit\Models\Quiz;

use App\Models\Quiz;
use App\Models\Mushroom;
use App\Models\QuizMushroom;
use Tests\TestCase;

class QuizTest extends TestCase
{
    private Quiz $quiz;

    public function setUp(): void
    {
        parent::setUp();

        $this->quiz = new Quiz([
            'name' => 'Quiz de Identificação de Cogumelos',
            'description' => 'Teste seus conhecimentos sobre cogumelos.',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $this->quiz->save();
    }

    public function test_should_create_a_new_quiz(): void
    {
        $quiz = new Quiz([
            'name' => 'Quiz de Toxicidade',
            'description' => 'Saiba quais cogumelos são venenosos.',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertTrue($quiz->save());
        $this->assertNotNull($quiz->id);
        $this->assertCount(2, Quiz::all());
    }

    public function test_all_should_return_all_quizzes(): void
    {
        $quizzes = Quiz::all();
        $this->assertCount(1, $quizzes);
        $this->assertEquals($this->quiz->name, $quizzes[0]->name);
    }

    public function test_find_by_id_should_return_the_quiz(): void
    {
        $found = Quiz::findById($this->quiz->id);
        $this->assertNotNull($found);
        $this->assertEquals($this->quiz->name, $found->name);
    }

    public function test_quiz_is_invalid_without_name(): void
    {
        $quiz = new Quiz([
            'name' => '',
            'description' => 'Descrição inválida',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertFalse($quiz->isValid());
        $this->assertTrue($quiz->hasErrors());
        $this->assertNotNull($quiz->errors('name'));
        $this->assertStringContainsString('não pode ser vazio', $quiz->errors('name'));
    }

    public function test_quiz_is_invalid_when_name_is_not_unique(): void
    {
        $duplicate = new Quiz([
            'name' => $this->quiz->name,
            'description' => 'Outro quiz com o mesmo nome',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->assertFalse($duplicate->isValid());
        $this->assertTrue($duplicate->hasErrors());
        $this->assertNotNull($duplicate->errors('name'));
        $this->assertStringContainsString('já existe um registro com esse dado', $duplicate->errors('name'));
    }

    public function test_should_return_empty_quiz_mushrooms_if_none_linked(): void
    {
        $this->assertEmpty($this->quiz->quizMushrooms());
        $this->assertEmpty($this->quiz->mushrooms());
    }

    public function test_should_return_mushrooms_linked_to_quiz(): void
    {
        $m1 = new Mushroom([
            'scientific_name' => 'Cantharellus cibarius',
            'image_url' => 'https://example.com/cantharellus.jpg',
            'hint' => 'Chanterelle',
            'description' => 'Edible yellow mushroom.',
        ]);
        $m1->save();

        $m2 = new Mushroom([
            'scientific_name' => 'Amanita phalloides',
            'image_url' => 'https://example.com/amanita.jpg',
            'hint' => 'Very toxic',
            'description' => 'Deadly poisonous mushroom.',
        ]);
        $m2->save();

        $link1 = new QuizMushroom(['quiz_id' => $this->quiz->id, 'mushroom_id' => $m1->id]);
        $link2 = new QuizMushroom(['quiz_id' => $this->quiz->id, 'mushroom_id' => $m2->id]);
        $link1->save();
        $link2->save();

        $quizMushrooms = $this->quiz->quizMushrooms();
        $this->assertCount(2, $quizMushrooms);

        $mushrooms = $this->quiz->mushrooms();
        $this->assertCount(2, $mushrooms);
        $this->assertEqualsCanonicalizing(
            [$m1->id, $m2->id],
            array_map(fn($m) => $m->id, $mushrooms)
        );
    }

    public function test_destroy_should_delete_quiz_and_links(): void
    {
        $m = new Mushroom([
            'scientific_name' => 'Lactarius deliciosus',
            'image_url' => 'https://example.com/lactarius.jpg',
            'hint' => 'Delicious mushroom',
            'description' => 'A prized edible mushroom.',
        ]);
        $m->save();

        $link = new QuizMushroom([
            'quiz_id' => $this->quiz->id,
            'mushroom_id' => $m->id
        ]);
        $link->save();

        QuizMushroom::deleteWhere(['quiz_id' => $this->quiz->id]);
        $this->quiz->destroy();

        $this->assertNull(Quiz::findById($this->quiz->id));
        $this->assertEmpty(QuizMushroom::where(['quiz_id' => $this->quiz->id]));
    }
}
