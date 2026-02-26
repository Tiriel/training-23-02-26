<?php

namespace App\Search;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

//#[When('prod')]
#[AsDecorator(ConferenceSearchInterface::class)]
class CacheableConferenceSearch implements ConferenceSearchInterface
{
    public function __construct(
        private readonly ConferenceSearchInterface $inner,
        private readonly CacheInterface $cache,
        private readonly SluggerInterface $slugger,
    ) {}

    public function searchByName(?string $name = null): array
    {
        $slug = $this->slugger->slug($name ?? 'no-name');

        return $this->cache->get($slug, function (ItemInterface $item) use ($name) {
            $item
                ->expiresAfter(3600)
                ->set($this->inner->searchByName($name));

            return $item->get();
        });
    }
}
