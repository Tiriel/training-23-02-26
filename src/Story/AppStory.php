<?php

namespace App\Story;

use App\Factory\ConferenceFactory;
use App\Factory\OrganizationFactory;
use App\Factory\UserFactory;
use App\Factory\VolunteeringFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        UserFactory::new()
            ->email('admin@sensio-events.com')
            ->password('admin')
            ->roles(['ROLE_ADMIN'])
            ->create();
        UserFactory::new()
            ->email('organizer@sensio-events.com')
            ->password('organizer')
            ->roles(['ROLE_ORGANIZER'])
            ->create();
        UserFactory::new()
            ->email('user@sensio-events.com')
            ->password('user')
            ->create();
        UserFactory::createMany(5);
        OrganizationFactory::createMany(6);
        ConferenceFactory::createMany(20);
        VolunteeringFactory::createMany(10);
    }
}
