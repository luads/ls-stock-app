<?php

declare(strict_types=1);

namespace App\Share\Quandl;

use App\Share\ApiClientInterface;
use App\Share\ShareDetails;

class ApiClient implements ApiClientInterface
{
    public function get(string $name): ShareDetails
    {
        return new ShareDetails($name, 1);
    }
}
