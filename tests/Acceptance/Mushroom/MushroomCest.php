<?php

namespace Tests\Acceptance;

use App\Models\User;
use Tests\Support\AcceptanceTester;

class MushroomsCest extends BaseAcceptanceCest
{
    private function login(AcceptanceTester $I): void
    {
        $user = new User([
            'name'                  => 'User 1',
            'email'                 => 'fulano@example.com',
            'password'              => '123456',
            'password_confirmation' => '123456',
            'is_admin'              => 0
        ]);
        $user->save();

        $I->amOnPage('/login');
        $I->fillField('user[email]', $user->email);
        $I->fillField('user[password]', '123456');
        $I->click('Entrar');
        $I->seeCurrentUrlEquals('/mushrooms');
    }

    private function createMushroom(
        AcceptanceTester $I,
        string $scientificName,
        string $imageUrl,
        string $hint,
        string $description
    ): void {
        $I->amOnPage('/mushrooms/new');
        $I->fillField('mushroom[scientific_name]', $scientificName);
        $I->fillField('mushroom[image_url]', $imageUrl);
        $I->fillField('mushroom[hint]', $hint);
        $I->fillField('mushroom[description]', $description);
        $I->click('Registrar');
        $I->seeCurrentUrlEquals('/mushrooms');
        $I->see('Cogumelo registrado com sucesso!', '.alert-success');
    }

    public function testIndexPageShowsMushrooms(AcceptanceTester $I)
    {
        $this->login($I);

        $I->see('Registrar Cogumelo');
        $I->click('Registrar Cogumelo');

        $I->see('Nome Científico', 'label');
        $I->see('URL da Imagem', 'label');
        $I->see('Dica', 'label');
        $I->see('Descrição', 'label');

        $this->createMushroom(
            $I,
            'Boletus edulis',
            'http://example.com/boletus.jpg',
            'Porcini mushroom',
            'A highly prized edible mushroom.'
        );
        $I->see('Boletus edulis');
    }

    public function testNewMushroomPage(AcceptanceTester $I): void
    {
        $this->login($I);

        $I->amOnPage('/mushrooms/new');
        $I->see('Novo Cogumelo', 'h1');
        $I->seeElement('form[action="/mushrooms"]');
    }

    public function testCreateMushroomSuccessfully(AcceptanceTester $I): void
    {
        $this->login($I);
        $this->createMushroom(
            $I,
            'Amanita phalloides',
            'http://example.com/deathcap.jpg',
            'Death cap',
            'Highly poisonous mushroom.'
        );
        $I->see('Amanita phalloides');
    }

    public function testCreateMushroomWithValidationErrors(AcceptanceTester $I): void
    {
        $this->login($I);

        $I->amOnPage('/mushrooms/new');
        $I->fillField('mushroom[scientific_name]', null);
        $I->fillField('mushroom[image_url]', 'http://example.com/invalid.jpg');
        $I->fillField('mushroom[hint]', 'Invalid entry');
        $I->fillField('mushroom[description]', 'Should have validation errors.');
        $I->click('Registrar');

        $I->see('Existem dados incorretos! Por favor, verifique!', '.alert-danger');
        $I->see('não pode ser vazio', '.invalid-feedback');
    }

    public function testEditMushroomPage(AcceptanceTester $I): void
    {
        $this->login($I);
        $this->createMushroom(
            $I,
            'Morchella esculenta',
            'http://example.com/morel.jpg',
            'Morel mushroom',
            'Edible sac fungi, highly sought after.'
        );

        $I->see('Morchella esculenta');
        $I->click(['css' => 'tbody tr:nth-child(1) a.btn-link']);

        $I->see('Editar Cogumelo', 'h1');
        $I->seeElement('form[action^="/mushrooms/"][method="post"]');
    }

    public function testUpdateMushroomSuccessfully(AcceptanceTester $I): void
    {
        $this->login($I);
        $this->createMushroom(
            $I,
            'Fomes fomentarius',
            'http://example.com/tindercfungus.jpg',
            'Tinder fungus',
            'Used to make tinder for starting fires.'
        );

        $I->see('Fomes fomentarius');
        $I->click(['css' => 'tbody tr:nth-child(1) a.btn-link']);

        $I->fillField('mushroom[scientific_name]', 'Fomes fomentarius Updated');
        $I->fillField('mushroom[hint]', 'Updated hint for tinder fungus');
        $I->click('Atualizar');

        $I->seeCurrentUrlEquals('/mushrooms');
        $I->see('Cogumelo atualizado com sucesso!', '.alert-success');
        $I->see('Fomes fomentarius Updated');
    }

    public function testUpdateMushroomWithValidationErrors(AcceptanceTester $I): void
    {
        $this->login($I);
        $this->createMushroom(
            $I,
            'Ganoderma lucidum',
            'http://example.com/reishi.jpg',
            'Reishi mushroom',
            'Used in traditional medicine.'
        );

        $I->see('Ganoderma lucidum');
        $I->click(['css' => 'tbody tr:nth-child(1) a.btn-link']);

        $I->fillField('mushroom[scientific_name]', '');
        $I->click('Atualizar');

        $I->seeInCurrentUrl('/mushrooms/');
        $I->see('Existem dados incorretos! Por favor, verifique!', '.alert-danger');
        $I->see('não pode ser vazio', '.invalid-feedback');
    }

    public function testDeleteMushroom(AcceptanceTester $I): void
    {
        $this->login($I);
        $this->createMushroom(
            $I,
            'Hypholoma fasciculare',
            'http://example.com/sulfur_tuft.jpg',
            'Sulfur tuft',
            'Poisonous mushroom growing in clusters.'
        );

        $I->see('Hypholoma fasciculare');
        $I->click(['css' => 'tbody tr:nth-child(1) form button']);

        $I->seeCurrentUrlEquals('/mushrooms');
        $I->see('Cogumelo removido com sucesso!', '.alert-success');
        $I->dontSee('Hypholoma fasciculare');
    }

    public function testMushroomPagination(AcceptanceTester $I): void
    {
        $this->login($I);

        for ($i = 1; $i <= 12; $i++) {
            $this->createMushroom(
                $I,
                "Paginated Mushroom {$i}",
                "http://example.com/mushroom{$i}.jpg",
                "Hint {$i}",
                "Description {$i}"
            );
        }

        // verificar apenas que aparecem "Paginated Mushroom 1" a "Paginated Mushroom 5"
        $I->amOnPage('/mushrooms?page=1');
        for ($i = 1; $i <= 5; $i++) {
            $I->see("Paginated Mushroom {$i}");
        }

        // verificar apenas que aparecem "Paginated Mushroom 6" a "Paginated Mushroom 10"
        $I->amOnPage('/mushrooms?page=2');
        for ($i = 6; $i <= 10; $i++) {
            $I->see("Paginated Mushroom {$i}");
        }

        // verificar apenas que aparecem "Paginated Mushroom 11" e "Paginated Mushroom 12"
        $I->amOnPage('/mushrooms?page=3');
        for ($i = 11; $i <= 12; $i++) {
            $I->see("Paginated Mushroom {$i}");
        }
    }
}
