<?php

namespace App\Search\Interface;

interface EntityTransformerInterface
{
    public function transform(array $data): object;
}
