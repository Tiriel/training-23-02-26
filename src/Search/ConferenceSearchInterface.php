<?php

namespace App\Search;

interface ConferenceSearchInterface
{
    public function searchByName(?string $name = null): array;
}
