<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme;
use InvalidArgumentException;

/**
 * Understands and resolves dependencies through proxy
 * resolvers and/or declared dependencies; also exposes
 * the methods to make calls with those dependencies.
 */
class Resolver
{
    /**
     * If a provided resolver is an object instance,
     * this is the method that will be called on it.
     * @var string
     */
    const RESOLVER_METHOD = 'resolve';

    /**
     * Registered resolver proxies, that know how
     * to retrieve dependencies by name/identifier.
     * @var array
     */
    protected $resolvers = array();

    /**
     * Dependency identifier aliases, so that alternate
     * names may be provided for dependencies.
     * @see Gimme\Resolver::alias
     * @var array
     */
    protected $aliases = array();

    /**
     * Registers a dependency resolver. Resolvers are
     * queried for a dependency in the order they are
     * registered.
     * @param  callable|object|Gimme\DependencyResolver\DependencyResolverInterface $resolver
     * @return $this
     */
    public function pushResolver($resolver)
    {
        if(is_callable($resolver) || method_exists($resolver, self::RESOLVER_METHOD)) {
            $this->resolvers[] = $resolver;
        } else {
            throw new InvalidArgumentException(
                "Resolver must be callable, or expose '" . self::RESOLVER_METHOD . "' public method"
            );
        }

        return $this;
    }

    /**
     * Pops the last resolver from the resolver stack,
     * and returns it.
     * @return callable|object
     */
    public function popResolver() { return array_pop($this->resolvers); }
}
