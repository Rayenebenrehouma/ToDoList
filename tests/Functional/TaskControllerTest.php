<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    public function testIfCreateTaskIsSuccessfull(): void
    {
        $client = static::createClient();


        //Recup url generator
        $urlGenerator = $client->getContainer()->get('router');
        // recup l'entity manager permet de récupérer un utilisateur ou une task dans un autre cas de figure
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        //on selectionne et connect avec le compte nous permettant de créer un client
        $user = $entityManager->find(User::class, 10);

        $client->loginUser($user);

        //Se rendre sur la page de création d'une task
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('task_create'));

        //Gérer un formulaire
        $submitButton = $crawler->selectButton('Mettre à jour');
        $form = $submitButton->form();
        $form["task[title]"] = "PHPUNIT";
        $form["task[content]"] = "PHPUNIT CONTENT";

        $client->submit($form);

        //Gérer la redirection
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        //Gérer l'alerte box et la route
        //$this->assertSelectorTextContains('div.alert-success', 'Votre task a été créer avec succès !');
    }
}
