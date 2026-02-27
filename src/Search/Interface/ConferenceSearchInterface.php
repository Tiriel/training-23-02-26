<?php

namespace App\Search\Interface;

interface ConferenceSearchInterface
{
    public function searchByName(?string $name = null): array;
}
