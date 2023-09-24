<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use ReflectionException;
use Saloon\Exceptions\InvalidResponseClassException;
use Saloon\Exceptions\PendingRequestException;

class GenerateDomain
{
    use AsAction;

    /**
     * @throws InvalidResponseClassException
     * @throws ReflectionException
     * @throws PendingRequestException
     */
    public function handle(): string
    {
        return str($this->formatSubDomain(config('services.forge.git.branch')))
            ->append('.', config('services.forge.domain'))
            ->toString();
    }

    private function formatSubDomain($branch): string
    {
        $pattern = config('services.forge.subdomain.pattern');

        if (empty($pattern)) {
            return $branch;
        }

        preg_match($pattern, $branch, $new);

        return strtolower(current($new));
    }
}
