<?php declare(strict_types=1);

// tests/Controller/MainPageControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainPageControllerTest extends WebTestCase
{
    public function testMainPageAndClickLoginLink(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html title:contains("SIECIQ")')->count()
        );

        $link = $crawler
            ->filter('a:contains("Logowanie")')
            ->eq(0)
            ->link()
        ;
        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html title:contains("Logowanie")')->count()
        );
    }
}
