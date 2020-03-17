<?php

declare(strict_types=1);

// tests/Controller/AllPageControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AllPageControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/logowanie');

        $form = $crawler->selectButton('_submit')->form();
        $form['_username'] = 'user';
        $form['_password'] = '!@#$%^&*()';
        $this->client->submit($form);
    }

    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessful(string $url): void
    {
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function provideUrls(): array
    {
        return [
            ['/'],
            ['/rejestracja/'],
            ['/logowanie'],
            ['/konto'],
            ['/konto,1,strona,1'],
            ['/konto/uzytkownik,1,edycja'],
//            ['/konto/strona,1,edycja'],
            ['/admin'],
            ['/admin,1,strona,1'],
//            ['/admin/strona,1,akceptacja'],
            ['/link'],
            ['/pokaz'],
            ['/regulamin'],
            ['/prywatnosc'],
            ['/pomoc'],
            ['/kontakt']
        ];
    }
}
