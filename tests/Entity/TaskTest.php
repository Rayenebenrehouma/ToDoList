<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskTest extends WebTestCase
{
    public function testIfTaskEntityIsSuccessfull(): void
    {
        $container = static::getContainer();

        $user = new User();

        $task = new Task();
        $task->setUser($user);
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setContent("Task #1");
        $task->setTitle("Title #1");
        $task->setIsDone(true);

        $errors = $container->get('validator')->validate($task);

        $this->assertCount(0, $errors);
    }
}
