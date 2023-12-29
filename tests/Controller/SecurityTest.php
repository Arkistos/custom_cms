<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function PHPUnit\Framework\assertEquals;

class SecurityTest extends WebTestCase
{
    public ?KernelBrowser $client = null;
    public ?UserRepository $userRepository = null;
    public ?AbstractDatabaseTool $databaseTool = null;

    public function setUp():void{
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
                UserFixtures::class
        ]);
    }

    public function testLoginForm(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->client->submitForm(
            'Connexion', [
            'login_form[email]' => 'user@email.com',
            'login_form[password]' => 'password'
            ]
        );
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(),'/admin');
    }

    public function testInvalidEmail():void{
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->client->submitForm(
            'Connexion', [
            'login_form[email]' => 'us@email.com',
            'login_form[password]' => 'password'
            ]
        );
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(),'/login');
        $this->assertSelectorTextContains('p.alert', 'Invalid credentials');
    }
}
