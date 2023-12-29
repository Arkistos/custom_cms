<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public ?KernelBrowser $client = null;
    public function setUp():void{
        $this->client = static::createClient();
    }

    public function testUserPage(): void
    {
        $this->client->request('GET', '/admin/users');
        $this->assertResponseIsSuccessful();
    }
}
