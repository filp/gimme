<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme;
use Gimme\Exception\UnknownServiceException;
use InvalidArgumentException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Understands and resolves services through registered
 * providers. Also exposes methods to call other methods
 * while *~ magically ~* injecting services.
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
     * Should an exception be thrown if all options for
     * resolving a service are exhausted?
     * @var bool
     */
    private $throwOnMissingService = false;

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
     * @see Gimme\Resolver::throwOnMissingService
     * @param  bool|null $throw
     * @return bool
     */
    public function throwOnMissingService($throw = false)
    {
        if(func_num_args() != 0) {
            $this->throwOnMissingService = (bool) $throw;
        }

        return $this->throwOnMissingService;
    }

    /**
     * Adds an arbitrary alias from one service identifier to another. If the alias is
     * provided, it will be mapped before dispatching to proxy providers.
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
     * Given a callable, automagically figures out how to inject the services
     * argument, and returns a bound callable that can be called whenever.
     * @param  callable $callable
     * @return callable
     */
    public function bind($callable)
    {
        if(!is_callable($callable)) {
            throw new InvalidArgumentException(
                'Argument to ' . __METHOD__ . ' must be a callable'
            );
        }

        // This is almost certainly wrong:
        if(is_array($callable)) {
            $reflection = new ReflectionMethod($callable[0], $callable[1]);
        } else {
            $reflection = new ReflectionFunction($callable);
        }

        // Iterate through each of the parameters expected by the callable,
        // and use the parameter names to attempt to match known services.
        $parameters    = $reflection->getParameters();
        $resolver      = $this;
        $boundCallable = function() use($parameters, $reflection, $callable, $resolver) {
            $services  = array();
            $args      = func_get_args();

            // The LAST argument must be called 'services', and be an array:
            $param = end($parameters);
            if($param->name == 'services' && is_array($serviceList = $param->getDefaultValue())) {
                foreach($serviceList as $service) {
                    $services[$service] = $resolver->resolve($service);
                }

                // Insert the argument in the correct (???) order, while
                // preserving additional arguments:
                $expectsParameters  = $reflection->getNumberOfParameters();

                $baseArgumentsSlice  = array_slice($args, 0, $expectsParameters - 1);
                $extraArgumentsSlice = array_slice($args, count($baseArgumentsSlice));

                // Append the services array in its correct position, between expected
                // arguments and extra arguments:
                $baseArgumentsSlice[] = $services;

                // And everything else after it:
                $args = array_merge($baseArgumentsSlice, $extraArgumentsSlice);
            }

            // Additional arguments passed to the outer/bound function
            // are appended to the end of the arguments list.
            return call_user_func_array($callable, $args);
        };

        return $boundCallable;
    }

    /**
     * Resolves a service by name.
     * @todo   implement
     * @param  string $serviceIdentifier
     * @throws Gimme\Exception\UnknownDependencyException if service not
     *             found and throwOnMissingService(true)
     * @return mixed|null
     */
    public function resolve($serviceIdentifier)
    {
        if(!empty($this->aliases[$serviceIdentifier])) {
            $serviceIdentifier = $this->aliases[$serviceIdentifier];
        }

        foreach($this->providers as $i => $provider) {
            $callable   = is_callable($provider) ? $provider : array($provider, self::PROVIDER_METHOD);
            $resolution = call_user_func($provider, $serviceIdentifier);

            if($resolution !== null) {
                return $resolution;
            }
        }

        // Nothing was found if we got this far.
        if($this->throwOnMissingService()) {
            throw new UnknownServiceException(
                "Unknown service identifier: $serviceIdentifier"
            );
        } else { return null; }
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
