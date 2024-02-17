<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    public function testIfUserEntityIsSuccessfull(): void
    {
        $container = static::getContainer();

        $user = new User();
        $user->setEmail('Test@gmail.com');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword("****");
        $user->setUsername('Rayen');

        $errors = $container->get('validator')->validate($user);

        $this->assertCount(0, $errors);
    }
}
