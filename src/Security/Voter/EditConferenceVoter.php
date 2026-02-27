<?php

namespace App\Security\Voter;

use App\Entity\Conference;
use App\Security\Attributes;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class EditConferenceVoter implements VoterInterface
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $checker,
    ) {}

    /**
     * @inheritDoc
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        $user = $token->getUser();
        if (
            null === $user
            || !$subject instanceof Conference
            || !\in_array(Attributes::CONFERENCE_EDIT, $attributes)
        ) {
            return self::ACCESS_ABSTAIN;
        }

        if ($this->checker->isGranted('ROLE_WEBSITE')) {
            return self::ACCESS_GRANTED;
        }

        return $user === $subject->getCreatedBy()
            ? self::ACCESS_GRANTED
            : self::ACCESS_DENIED;
    }
}
