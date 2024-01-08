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

    public function testForgetPasswordLink():void{
        $this->client->request('GET', '/login');
        $this->assertSelectorTextContains('a.forgetPassword', 'Mot de passe oublié');
        $this->client->clickLink('Mot de passe oublié');
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/forgetPassword');
    }

    public function testForgetPasswordPage():void{
        $this->client->request('GET', '/forgetPassword');
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorExists('input.forget_password_email');
        $this->assertSelectorExists('button.forget_password_submit');
    }
    
    public function testForgetPasswordMail():void{
        $this->client->request('GET', '/forgetPassword');
        $this->client->submitForm('Reinitialiser', ['reset_password_form[email]'=>'user@email.com']);
        $this->assertQueuedEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailAddressContains($email, 'to','user@email.com',);
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/login');
        $this->assertSelectorTextContains(
            'div.flash-success',
            'Le lien permettant de réinitialiser votre mot de passe vous à été envoyé'
        );
    }

    public function testForgetPasswordWrongEmail():void{
        $this->client->request('GET', '/forgetPassword');
        $this->client->submitForm('Reinitialiser', ['reset_password_form[email]'=>'wronguser@email.com']);
        $this->assertQueuedEmailCount(0);
        $this->client->followRedirect();
        $this->assertEquals($this->client->getRequest()->getPathInfo(), '/login');
        $this->assertSelectorTextContains('div.flash-alert', "Cet email n'est pas inscrit");
    }
}
