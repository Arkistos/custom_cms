<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public ?KernelBrowser $client = null;

    public function setUp():void{
        $this->client = static::createClient();
    }

    public function testAdminPage(): void
    {
        $this->client->request('GET', '/admin');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('a.button', 'Gestion des utilisateurs');
        $this->client->clickLink('Gestion des utilisateurs');
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/admin/users');
    }
}
