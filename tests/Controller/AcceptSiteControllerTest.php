<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AcceptSiteControllerTest extends WebTestCase
{
    public function testAcceptSitePageAndAcceptSite(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/logowanie');

        $form = $crawler->selectButton('_submit')->form();
        $form['_username'] = 'user';
        $form['_password'] = '!@#$%^&*()';
        $client->submit($form);

        $crawler = $client->request('GET', '/admin');

        $link = $crawler
            ->filter('a:contains("Test")')
            ->eq(0)
            ->link()
        ;
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html title:contains("Akceptacja strony")'
            )->count()
        );

        $form = $crawler->selectButton('accept_site_form[save]')->form();
        $form['accept_site_form[name]'] = 'Test.pl';
        $form['accept_site_form[url]'] = 'http://test.pl';
        $form['accept_site_form[active]']->select('1');
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter(
                'html p.ok:contains("Strona www została zaakceptowana.")'
            )->count()
        );
    }
}
