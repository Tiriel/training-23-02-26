<?php

namespace App\Search\Transformer;

use App\Entity\Organization;
use App\Search\Interface\EntityTransformerInterface;

class ApiOrganizationTransformer implements EntityTransformerInterface
{
    public function transform(array $data): Organization
    {
        return (new Organization())
            ->setName($data['name'])
            ->setPresentation($data['presentation'])
            ->setCreatedAt(new \DateTimeImmutable($data['createdAt'] ?? 'now'));
    }

}
