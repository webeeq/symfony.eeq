<?php declare(strict_types=1);

// tests/Controller/UserAccountControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAccountControllerTest extends WebTestCase
{
    public function testLoginAccountAndAddSite(): void
    {
        $client = static::createClient();
        $client->request('GET', '/konto');
        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html title:contains("Logowanie")')->count()
        );

        $form = $crawler->selectButton('_submit')->form();
        $form['_username'] = 'user';
        $form['_password'] = '!@#$%^&*()';
        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html title:contains("Konto")')->count()
        );

        $form = $crawler->selectButton('user_account_form[save]')->form();
        $form['user_account_form[name]'] = 'Test';
        $form['user_account_form[url]'] = 'http://www.test.pl';
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html p.ok:contains("Strona www została dodana i oczekuje '
                    . 'na akceptację.")'
            )->count()
        );
    }
}
