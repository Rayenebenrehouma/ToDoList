<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    const TASK_EDIT = 'task_edit';
    const TASK_DELETE = 'task_delete';

    private $security;

    public function __construct(Security $security){
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $task): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::TASK_EDIT, self::TASK_DELETE])
            && $task instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $task, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        //On vérifie que l'utilisateur est admin
        if($this->security->isGranted('ROLE_ADMIN')) return true;

        // On vérifie si la task a un propriétaire
        if(null === $task->getUser()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::TASK_EDIT:
                //On vérifie si on peut éditer
                return $this->canEdit($task, $user);
                break;
            case self::TASK_DELETE:
                // On vérifie si on peut supprimer
                return $this->canDelete($task, $user);
                break;
        }

        return false;
    }

    private function canEdit(Task $task, User $user){
        //Le propriétaire de la task peut la modifier
        return $user === $task->getUser();
    }

    private function canDelete(Task $task, User $user){
        //Le propriétaire de la task peut la supprimer
        return $user === $task->getUser();
    }
}
