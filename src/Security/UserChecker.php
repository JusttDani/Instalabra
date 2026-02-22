<?php

namespace App\Security;

use App\Entity\Usuario;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Usuario) {
            return;
        }

        if ($user->isBlocked()) {
            // Este mensaje se lanzará y el usuario se topará con él si tiene la cuenta baneada
            throw new CustomUserMessageAccountStatusException('Tu cuenta de usuario está bloqueada.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof Usuario) {
            return;
        }
    }
}
