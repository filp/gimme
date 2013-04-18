<?php
/**
 * Gimme - inject your services and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme\ServiceProvider;

/**
 * Interface for service providers. Duck-typing is employed,
 * so using this interface is optional. You will notice this
 * interface is not actually used anywhere in the Gimme code-base.
 */
interface ServiceProviderInterface
{
    /**
     * Resolves and returns a service by its identifier,
     * or 'null' if such a service is not recognized.
     * @param  string $serviceIdentifier
     * @return null|mixed
     */
    public function resolve($serviceIdentifier);
}
