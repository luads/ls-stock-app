<?php

declare(strict_types=1);

namespace App\Share;

interface ApiClientInterface
{
    public function get(string $name): ShareDetails;
}
