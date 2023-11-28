<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/users', name: 'user_list')]
    public function ListAction(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig',[
            'users' => $users
        ]);
    }

    #[Route('/users/create', name: 'user_create')]
    public function createAction(Request $request, UserPasswordHasherInterface $hasher){
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();
            $password = $hasher->hashPassword($user, $user->getPassword());

            $user->setPassword($password);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre compte a bien été créer');


            return $this->redirectToRoute('home');
        }

        return $this->render('user/create.html.twig',[
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editAction(User $user,Request $request,UserPasswordHasherInterface $hasher, $id){
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $form = $this->createForm(EditUserType::class, $user);

        $form->handleRequest($request);

        if ($user){
            if ($form->isSubmitted() && $form->isValid()){
                $new_pwd = $form->get('password')->getData();
                $password = $hasher->hashPassword($user, $new_pwd);

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', "L'utilisateur a bien été modifié");

                return $this->redirectToRoute('user_list');
            }
        }
            return $this->render('user/edit.html.twig',[
                'user' => $user,
                'form' => $form
            ]);
    }
}
