<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    public ?KernelBrowser $client = null;

    public function setUp():void{
        $this->client = static::createClient();
    }

    public function testLoginForm(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->client->submitForm(
            'Connexion', [
            'login_form[email]' => 'test@mail.com',
            'login_form[password]' => 'password'
            ]
        );
    }

    
}
