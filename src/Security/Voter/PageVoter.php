<?php

namespace App\Security\Voter;

use App\Entity\Page;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PageVoter extends Voter
{
    const DELETE = 'Delete';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Page;
    }

    protected function voteOnAttribute($attribute, $page, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $page->getUser() === $user;
//                return $this->canEdit($page, $user);
                break;
            case self::DELETE:
                // logic to determine if the user can VIEW
                // return true or false
                return $page->getUser() === $user;
                break;
        }

        return false;
    }


//    private function canEdit(Page $page, User $user): bool
//    {
//        $ownerId = $page->getUser()->getId();
//        $UserId = $user->getUser()->getId();
//        return $ownerId === $UserId;
//    }

}