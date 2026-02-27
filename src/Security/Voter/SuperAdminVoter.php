<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class SuperAdminVoter implements VoterInterface
{
    public function __construct(
        private readonly RoleHierarchyInterface $hierarchy,
    ) {}

    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return static::ACCESS_ABSTAIN;
        }

        if (\in_array('ROLE_SUPER_ADMIN', $this->hierarchy->getReachableRoleNames($user->getRoles()))) {
            return static::ACCESS_GRANTED;
        }

        //if (\in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
        //    return  static::ACCESS_GRANTED;
        //}

        return static::ACCESS_ABSTAIN;
    }
}
