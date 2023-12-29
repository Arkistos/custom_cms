<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function PHPUnit\Framework\assertEquals;

class UserTest extends WebTestCase
{
    public function testUser(): void
    {
        $user = new User();
        $user->setEmail('test@user.com');
        $user->setPassword('password');
        assertEquals('test@user.com', $user->getEmail());
        assertEquals('password', $user->getPassword());
    }
}
