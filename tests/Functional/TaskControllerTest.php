<?php

namespace App\Tests\Functional;

use App\Entity\Task;
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
        $submitButton = $crawler->selectButton('Créez une nouvelle tâche');
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

    public function testIfEditTaskIsSuccessfull(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 10);
        $task = $entityManager->getRepository(Task::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('task_edit', ['id' => $task->getId()])
        );

        $this->assertResponseIsSuccessful();

        $submitButton = $crawler->selectButton('Mettre à jour');
        $form = $submitButton->form();
        $form["edit_task[title]"] = "NEW PHPUNIT";
        $form["edit_task[content]"] = "NEW PHPUNIT CONTENT";
        $form["edit_task[isDone]"] = false;

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();


    }

    public function testIfDeleteTaskIsSuccessfull(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 10);
        $task = $entityManager->getRepository(Task::class)->findOneBy([
            'user' => $user
        ]);

        $client->loginUser($user);

        //$id = 12;

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('task_delete', ['id' => $task->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
    }
}
