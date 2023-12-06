<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\EditTaskType;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/task', name: 'task_list')]
    public function ListAction(Request $request): Response
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        return $this->render('task/list.html.twig',[
            'tasks' => $tasks
        ]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request){
        $task = new Task();
        $user = $this->getUser();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($user){
            if ($form->isSubmitted() && $form->isValid()) {
                $task =$form->getData();
                $date = new \DateTimeImmutable();
                $date->format("d/m/Y H:i:s");
                $task->setCreatedAt($date);
                $task->setUser($user);

                $this->entityManager->persist($task);
                $this->entityManager->flush();
                $this->addFlash('success','Votre Task a bien été créer !');

                return $this->redirectToRoute('home');
            }
        }

        return $this->render('task/create.html.twig',[
            'task' => $task,
            'form' => $form->createView()
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(Task $task,Request $request, $id){
        $this->denyAccessUnlessGranted('task_edit', $task);
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        $form = $this->createForm(EditTaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isSubmitted()){
            $task = $form->getData();
            $date = new \DateTimeImmutable();
            $date->format("d/m/Y H:i:s");
            $task->setCreatedAt($date);

            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->addFlash('success','Votre tâche a bien été modifié !');

            return $this->redirectToRoute('home');
        }

        return $this->render('task/edit.html.twig',[
           'task' => $task,
           'form' => $form->createView()
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task, Request $request, $id){
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        $status = $task->getIsDone();

        if ($status == false){
            $task->setIsDone(true);
        }else{
            $task->setIsDone(false);
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->addFlash('success','La tâche a bien été notée comme étant faite !');

        return $this->redirectToRoute('home');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task, Request $request, $id){

        $task = $this->entityManager->getRepository(Task::class)->find($id);
        $user = $this->getUser();


        if ($user === $task->getUser()){
            $this->entityManager->remove($task);
            $this->entityManager->flush();

            $this->addFlash('success','Votre tâche a bien été supprimer !');
            return $this->redirectToRoute('home');
        }else{
            $this->addFlash('danger', 'Vous essayez de supprimer une tâche qui ne vous appartient pas !');
            return $this->redirectToRoute('task_list', status:403);
        }
    }
}
