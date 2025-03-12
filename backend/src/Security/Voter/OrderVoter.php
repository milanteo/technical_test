<?php

namespace App\Security\Voter;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class OrderVoter extends Voter {
    
    public const EDIT = 'VOTE_ORDER_EDIT';
    public const VIEW = 'VOTE_ORDER_VIEW';

    protected function supports(string $attribute, mixed $subject): bool {
        
        return in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof Order;
    }

    protected function voteOnAttribute(
        string $attribute, 
        mixed $subject, 
    TokenInterface $token): bool {

        $user = $token->getUser();

        if (!$user instanceof User) {

            return false;
        }

        switch ($attribute) {
            case self::EDIT:

                    return $subject->getCreatedBy()->getId() === $user->getId();
                break;

            case self::VIEW:
                    
                    return $subject->getCreatedBy()->getId() === $user->getId();
                break;
        }

        return false;
    }
}
