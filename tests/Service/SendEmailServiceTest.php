<?php

namespace App\Tests\Service;

use App\Service\SendEmailService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendEmailServiceTest extends WebTestCase
{

    private ?MailerInterface $mailer = null;

    public function setUp():void{
        $this->mailer = static::getContainer()->get(MailerInterface::class);
    }
    
    public function testSendEmail(): void
    {
        $sendMailService = new SendEmailService($this->mailer);
        $sendMailService->send('me@me.com', 'you@you.com','subject', 'text');
        $this->assertEmailCount(1);
    }
}
