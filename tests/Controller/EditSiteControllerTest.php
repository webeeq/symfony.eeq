<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EditSiteControllerTest extends WebTestCase
{
    public function testEditSitePageAndDeleteSite(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/logowanie');

        $form = $crawler->selectButton('_submit')->form();
        $form['_username'] = 'user';
        $form['_password'] = '!@#$%^&*()';
        $client->submit($form);

        $crawler = $client->request('GET', '/konto');

        $link = $crawler
            ->filter('a:contains("Test.pl")')
            ->eq(0)
            ->link()
        ;
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html title:contains("Edycja strony")'
            )->count()
        );

        $form = $crawler->selectButton('edit_site_form[save]')->form();
        $form['edit_site_form[delete]']->tick();
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html p.ok:contains("Dane strony www zostały usunięte.")'
            )->count()
        );
    }
}
