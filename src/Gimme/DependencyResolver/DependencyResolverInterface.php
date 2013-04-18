<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme\DependencyResolver;

/**
 * Interface for dependency resolvers. Duck-typing is employed,
 * so using this interface is optional. You will notice this
 * interface is not actually used anywhere in the Gimme code-base.
 */
interface DependencyResolverInterface
{
    /**
     * Resolves and returns a dependency by its identifier,
     * or 'null' if such a dependency is not recognized.
     * @param  string $dependencyIdentifier
     * @return null|mixed
     */
    public function resolve($dependencyIdentifier);
}
