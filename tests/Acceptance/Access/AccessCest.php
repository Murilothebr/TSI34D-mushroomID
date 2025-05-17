<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AccessCest
{
    public function authenticatedUserCanAccessMushroomsPage(AcceptanceTester $I): void
    {
        $email = 'fulano@example.com';
        $password = '123456';

        $I->amOnPage('/login');
        $I->fillField('user[email]', $email);
        $I->fillField('user[password]', $password);
        $I->click('Entrar');

        $I->seeInCurrentUrl('/mushrooms');
    }

    public function unauthenticatedUserCannotAccessMushroomsPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/logout');
        $I->amOnPage('/mushrooms');
        $I->seeInCurrentUrl('/login');
        $I->see('Faça login para continuar');
    }

    public function guestCanAccessLoginPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/logout');
        $I->amOnPage('/login');
        $I->see('Login');
    }

    public function authenticatedUserIsRedirectedFromLogin(AcceptanceTester $I): void
    {
        $email = 'fulano@example.com';
        $password = '123456';

        $I->amOnPage('/login');
        $I->fillField('user[email]', $email);
        $I->fillField('user[password]', $password);
        $I->click('Entrar');

        $I->amOnPage('/login');
        $I->dontSeeInCurrentUrl('/login');
        $I->seeInCurrentUrl('/mushrooms');
    }

    public function adminCanAccessAdminPage(AcceptanceTester $I): void
    {
        $email = 'admin@example.com';
        $password = 'admin123';

        $I->amOnPage('/login');
        $I->fillField('user[email]', $email);
        $I->fillField('user[password]', $password);
        $I->click('Entrar');

        $I->amOnPage('/admin');
        $I->see('Admin');
    }

    public function userCannotAccessAdminPage(AcceptanceTester $I): void
    {
        $email = 'fulano@example.com';
        $password = '123456';

        $I->amOnPage('/login');
        $I->fillField('user[email]', $email);
        $I->fillField('user[password]', $password);
        $I->click('Entrar');

        $I->amOnPage('/admin');
        $I->see('403 Forbidden');
    }

    public function guestIsRedirectedFromAdminPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/logout');
        $I->amOnPage('/admin');
        $I->seeInCurrentUrl('/login');
    }
}
