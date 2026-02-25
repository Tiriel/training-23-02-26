<?php

namespace App\Factory;

use App\Entity\Conference;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Conference>
 */
final class ConferenceFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Conference::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->realText(30),
            'description' => self::faker()->realText(500),
            'accessible' => self::faker()->boolean(),
            'startAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-10 years', '+5 year')),
            'organizations' => OrganizationFactory::randomRangeOrCreate(1, 3),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(Conference $conference): void {
                $conference->setEndAt($conference->getStartAt()->modify('+1 day'));
            })
        ;
    }
}
