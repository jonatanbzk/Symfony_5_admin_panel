<?php


namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RouteControllerTest extends WebTestCase
{

    public function testIndexPage()
    {
        $client = static::createClient();
        $client->Request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $client->Request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testH2andLinkIndexPage()
    {
        $client = static::createClient();
        $client->Request('GET', 'login');
        $this->assertSelectorTextContains('h2', 'Se connecter');
        $client->Request('GET', '/');
        $this->assertSelectorTextContains('h2', 'Se connecter');
        $this->assertSelectorTextContains('a', 'Mot de passe oublié ?');
    }

    public function testJoinPage()
    {
        $client = static::createClient();
        $client->Request('GET', '/join');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testH2andLinkJoinPage()
    {
        $client = static::createClient();
        $client->Request('GET', 'join');
        $this->assertSelectorTextContains('h2', 'Inscription');
        $this->assertSelectorTextContains('a', 'Je me connecte');
    }

    public function testPasswordNewPage()
    {
        $client = static::createClient();
        $client->Request('GET', '/password/new');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testH2andLinkPasswordNewPage()
    {
        $client = static::createClient();
        $client->Request('GET', '/password/new');
        $this->assertSelectorTextContains('a', 'Je me connecte');
    }

}