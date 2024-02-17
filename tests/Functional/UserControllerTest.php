<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends WebTestCase
{
    public function testIfUserListIsSuccessfull(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 13);

        $client->loginUser($user);

        $client->request(Request::METHOD_GET, $urlGenerator->generate('user_list'));

        $this->assertResponseIsSuccessful();
    }
}
