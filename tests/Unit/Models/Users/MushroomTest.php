<?php

namespace Tests\Unit\Models\Mushroom;

use App\Models\Mushroom;
use Tests\TestCase;

class MushroomTest extends TestCase
{
    private Mushroom $mushroom1;
    private Mushroom $mushroom2;

    public function setUp(): void
    {
        parent::setUp();

        $this->mushroom1 = new Mushroom([
            'scientific_name' => 'Agaricus bisporus',
            'image_url' => 'https://example.com/agaricus.jpg',
            'hint' => 'Common button mushroom',
            'description' => 'Widely cultivated edible mushroom, known as button, cremini, or portobello.'
        ]);
        $this->mushroom1->save();

        $this->mushroom2 = new Mushroom([
            'scientific_name' => 'Pleurotus ostreatus',
            'image_url' => 'https://example.com/pleurotus.jpg',
            'hint' => 'Oyster mushroom',
            'description' => 'Edible mushroom with a broad, fan-shaped cap.'
        ]);
        $this->mushroom2->save();
    }

    public function test_should_create_a_new_mushroom(): void
    {
        $newMushroom = new Mushroom([
            'scientific_name' => 'Cantharellus cibarius',
            'image_url' => 'https://example.com/chanterelle.jpg',
            'hint' => 'Chanterelle',
            'description' => 'A distinctive, funnel-shaped edible mushroom.'
        ]);
        $this->assertTrue($newMushroom->save());
        $this->assertCount(3, Mushroom::all());
    }

    public function test_all_should_return_all_mushrooms(): void
    {
        $allMushrooms = Mushroom::all();

        $this->assertCount(2, $allMushrooms);
        $this->assertEquals($this->mushroom1->id, $allMushrooms[0]->id);
        $this->assertEquals($this->mushroom2->id, $allMushrooms[1]->id);
    }

    public function test_find_by_id_should_return_the_mushroom(): void
    {
        $foundMushroom = Mushroom::findById($this->mushroom1->id);

        $this->assertNotNull($foundMushroom);
        $this->assertEquals($this->mushroom1->id, $foundMushroom->id);
        $this->assertEquals($this->mushroom1->scientific_name, $foundMushroom->scientific_name);
    }

    public function test_find_by_id_should_return_null_if_not_found(): void
    {
        $this->assertNull(Mushroom::findById(99999));
    }

    public function test_find_by_scientific_name_should_return_the_mushroom(): void
    {
        $foundMushroom = Mushroom::findByScientificName('Agaricus bisporus');

        $this->assertNotNull($foundMushroom);
        $this->assertEquals($this->mushroom1->id, $foundMushroom->id);
        $this->assertEquals('Agaricus bisporus', $foundMushroom->scientific_name);
    }

    public function test_find_by_scientific_name_should_return_null_if_not_found(): void
    {
        $this->assertNull(Mushroom::findByScientificName('NonExistent Mushroom'));
    }

    public function test_paginate_should_return_paginated_results(): void
    {
        for ($i = 3; $i <= 7; $i++) {
            $m = new Mushroom([
                'scientific_name' => "Mushroom {$i}",
                'image_url' => "url{$i}.jpg",
                'hint' => "hint{$i}",
                'description' => "desc{$i}"
            ]);
            $m->save();
        }

        $paginatorPage1 = Mushroom::paginate(page: 1, per_page: 5);
        $this->assertCount(5, $paginatorPage1->registers());

        $paginatorPage2 = Mushroom::paginate(page: 2, per_page: 5);
        $this->assertCount(2, $paginatorPage2->registers());
    }

    public function test_update_method_should_update_mushroom_attributes(): void
    {
        $updatedData = [
            'scientific_name' => 'Agaricus campestris',
            'hint' => 'Field mushroom'
        ];

        $this->assertTrue($this->mushroom1->update($updatedData));

        $updatedMushroom = Mushroom::findById($this->mushroom1->id);

        $this->assertEquals('Agaricus campestris', $updatedMushroom->scientific_name);
        $this->assertEquals('Field mushroom', $updatedMushroom->hint);
        $this->assertEquals('https://example.com/agaricus.jpg', $updatedMushroom->image_url);
    }

    public function test_save_method_should_update_existing_mushroom(): void
    {
        $this->mushroom1->scientific_name = 'Lactarius deliciosus';
        $this->mushroom1->image_url = 'https://example.com/saffron.jpg';
        $this->assertTrue($this->mushroom1->save());

        $updatedMushroom = Mushroom::findById($this->mushroom1->id);
        $this->assertEquals('Lactarius deliciosus', $updatedMushroom->scientific_name);
        $this->assertEquals('https://example.com/saffron.jpg', $updatedMushroom->image_url);
    }

    public function test_destroy_should_remove_the_mushroom(): void
    {
        $this->mushroom1->destroy();
        $this->assertCount(1, Mushroom::all());
        $this->assertNull(Mushroom::findById($this->mushroom1->id));
    }

    public function test_mushroom_is_valid_with_all_required_fields(): void
    {
        $mushroom = new Mushroom([
            'scientific_name' => 'Coprinus comatus',
            'image_url' => 'https://example.com/shaggy.jpg',
            'hint' => 'Shaggy ink cap',
            'description' => 'Common fungus often found in grassy areas.'
        ]);

        $this->assertTrue($mushroom->isValid());
        $this->assertFalse($mushroom->hasErrors());
    }

    public function test_mushroom_is_invalid_without_scientific_name(): void
    {
        $mushroom = new Mushroom([
            'scientific_name' => '',
            'image_url' => 'http://example.com/image.jpg',
            'hint' => 'Common edible mushroom',
            'description' => 'A widely cultivated edible mushroom.'
        ]);

        $this->assertFalse($mushroom->isValid());
        $this->assertTrue($mushroom->hasErrors());
        $this->assertNotNull($mushroom->errors('scientific_name'));
        $this->assertStringContainsString('não pode ser vazio', $mushroom->errors('scientific_name'));
    }

    public function test_mushroom_is_invalid_without_image_url(): void
    {
        $mushroom = new Mushroom([
            'scientific_name' => 'Inocybe geophylla',
            'image_url' => '',
            'hint' => 'Poisonous',
            'description' => 'Small, toxic mushroom with a conical cap.'
        ]);

        $this->assertFalse($mushroom->isValid());
        $this->assertTrue($mushroom->hasErrors());
        $this->assertNotNull($mushroom->errors('image_url'));
        $this->assertStringContainsString('não pode ser vazio', $mushroom->errors('image_url'));
    }

    public function test_mushroom_is_invalid_without_hint(): void
    {
        $mushroom = new Mushroom([
            'scientific_name' => 'Amanita muscaria',
            'image_url' => 'https://example.com/amanita.jpg',
            'hint' => '',
            'description' => 'Famous but poisonous mushroom.'
        ]);

        $this->assertFalse($mushroom->isValid());
        $this->assertTrue($mushroom->hasErrors());
        $this->assertNotNull($mushroom->errors('hint'));
        $this->assertStringContainsString('não pode ser vazio', $mushroom->errors('hint'));
    }

    public function test_mushroom_is_invalid_without_description(): void
    {
        $mushroom = new Mushroom([
            'scientific_name' => 'Boletus edulis',
            'image_url' => 'https://example.com/porcini.jpg',
            'hint' => 'Porcini',
            'description' => '',
        ]);

        $this->assertFalse($mushroom->isValid());
        $this->assertTrue($mushroom->hasErrors());
        $this->assertNotNull($mushroom->errors('description'));
        $this->assertStringContainsString('não pode ser vazio', $mushroom->errors('description'));
    }

    public function test_mushroom_is_invalid_when_scientific_name_is_not_unique(): void
    {
        $duplicateMushroom = new Mushroom([
            'scientific_name' => 'Agaricus bisporus',
            'image_url' => 'https://example.com/duplicate.jpg',
            'hint' => 'Duplicate hint',
            'description' => 'This should fail validation.'
        ]);

        $this->assertFalse($duplicateMushroom->isValid());
        $this->assertTrue($duplicateMushroom->hasErrors());
        $this->assertNotNull($duplicateMushroom->errors('scientific_name'));
        // Changed the expected string to match the Portuguese error message
        $this->assertStringContainsString('já existe um registro com esse dado', $duplicateMushroom->errors('scientific_name'));
    }

    public function test_uniqueness_validation_allows_updating_same_record(): void
    {
        $this->mushroom1->description = 'Updated description for Agaricus bisporus.';

        $this->assertTrue($this->mushroom1->save());
        $this->assertFalse($this->mushroom1->hasErrors());

        $updatedMushroom = Mushroom::findById($this->mushroom1->id);
        $this->assertEquals('Updated description for Agaricus bisporus.', $updatedMushroom->description);
    }
}
