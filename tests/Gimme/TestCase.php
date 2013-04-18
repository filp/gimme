<?php
/**
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Gimme;
use Gimme\Resolver;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Gimme\Resolver
     */
    protected function getResolver()
    {
        return new Resolver;
    }

    /**
     * Returns something that behaves like the
     * Gimme\ServiceProvider\ServiceProviderInterface
     * @return Gimme\ServiceProvider\ServiceProviderInterface
     */
    protected function getServiceProvider()
    {
        return m::mock('Gimme\ServiceProvider\ServiceProviderInterface');
    }

    /**
     * Returns a callable provider that knows about a single service.
     * @return Gimme\ServiceProvider\ServiceProviderInterface
     */
    protected function getServiceProviderCallable($service)
    {
        $service = (string) $service;
        return function($d) use($service) {
            if($d == $service) {
                return true;
            }
        };
    }
}
