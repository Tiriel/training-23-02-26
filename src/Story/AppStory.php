<?php

namespace App\Story;

use App\Factory\ConferenceFactory;
use App\Factory\OrganizationFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main')]
final class AppStory extends Story
{
    public function build(): void
    {
        OrganizationFactory::createMany(6);
        ConferenceFactory::createMany(20);
    }
}
