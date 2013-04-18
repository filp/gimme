<?php
/**
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Gimme;
use Gimme\Resolver;
use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function getResolver()
    {
        return new Resolver;
    }

    /**
     * Returns something that behaves like the
     * Gimme\DependencyResolver\DependencyResolverInterface
     * @return Gimme\DependencyResolver\DependencyResolverInterface
     */
    protected function getDependencyResolver()
    {
        return m::mock('Gimme\\DependencyResolver\DependencyResolverInterface');
    }
}
