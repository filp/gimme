<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme;
use Gimme\Exception\UnknownServiceException;
use InvalidArgumentException;

/**
 * Understands and resolves dependencies through proxy
 * resolvers and/or declared dependencies; also exposes
 * the methods to make calls with those dependencies.
 */
class Resolver
{
    /**
     * If a provider is an object instance,
     * this is the method that will be called on it.
     * @var string
     */
    const PROVIDER_METHOD = 'resolve';

    /**
     * Registered provider proxies, that know how
     * to retrieve services by identifier.
     * @var array
     */
    protected $providers = array();

    /**
     * Service identifier aliases, so that alternate names may be
     * provided for services
     * @see Gimme\Resolver::alias
     * @var array
     */
    protected $aliases = array();

    /**
     * Adds an arbitrary alias from one service identifier
     * to another. If the alias is provided, it will be mapped
     * before dispatching to proxy providers.
     * @param  string $alias
     * @param  string $concrete
     * @return $this
     */
    public function alias($alias, $concrete)
    {
        if(!is_string($alias) || !is_string($concrete)) {
            throw new InvalidArgumentException(
                __METHOD__ . ' expects both arguments to be strings'
            );
        }

        $this->aliases[$alias] = $concrete;
        return $this;
    }

    /**
     * Resolves a service by name.
     * @todo   implement
     * @param  string $serviceIdentifier
     * @throws Gimme\Exception\UnknownDependencyException if ...
     * @return mixed
     */
    public function resolve($serviceIdentifier)
    {
        if(!empty($this->aliases[$serviceIdentifier])) {
            $serviceIdentifier = $this->aliases[$serviceIdentifier];
        }

        foreach($this->providers as $i => $resolver) {
            $callable   = is_callable($resolver) ? $resolver : array($resolver, self::PROVIDER_METHOD);
            $resolution = call_user_func($resolver, $serviceIdentifier);

            if($resolution !== null) {
                return $resolution;
            }
        }

        // Nothing was found
        throw new UnknownServiceException(
            "Unknown service identifier: $serviceIdentifier"
        );
    }

    /**
     * Pushes a service provider to the stack. Providers are queried for a service
     * in the order they are registered.
     * @param  callable|object|Gimme\ServiceProvider\ServiceProviderInterface $provider
     * @return $this
     */
    public function pushProvider($provider)
    {
        if(is_callable($provider) || method_exists($provider, self::PROVIDER_METHOD)) {
            $this->providers[] = $provider;
        } else {
            throw new InvalidArgumentException(
                "Provider must be callable, or expose the '" . self::PROVIDER_METHOD . "' public method"
            );
        }

        return $this;
    }

    /**
     * Pops the last provider from the providers stack,
     * and returns it.
     * @return callable|object
     */
    public function popProvider() { return array_pop($this->providers); }
}
