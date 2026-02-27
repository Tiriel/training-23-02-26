<?php

namespace App\Search\Transformer;

use App\Entity\Conference;
use App\Entity\Organization;
use App\Search\Interface\EntityTransformerInterface;

class ApiConferenceTransformer implements EntityTransformerInterface
{
    public function transform(array $data): Conference
    {
        return (new Conference())
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setPrerequisites($data['prerequisites'])
            ->setAccessible($data['accessible'])
            ->setStartAt(new \DateTimeImmutable($data['startDate']))
            ->setEndAt(new \DateTimeImmutable($data['endDate']));
    }
}
