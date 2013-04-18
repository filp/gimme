<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme;
use Gimme\TestCase;
use Gimme\Exception\UnknownDependencyException;
use InvalidArgumentException;

class ResolverTest extends TestCase
{

    /**
     * @covers Gimme\Resolver::pushResolver
     * @covers Gimme\Resolver::alias
     * @covers Gimme\Resolver::resolve
     */
    public function testResolverAlias()
    {
        $r = $this->getResolver();

        // mock resolver for fiddlesticks:
        $depResolver = function($d) {
            if($d == 'fiddlestick-service') {
                return true;
            }
        };

        $r->pushResolver($depResolver);
        $r->alias('foo', 'fiddlestick-service');

        $this->assertTrue($r->resolve('foo'));
        $this->assertTrue($r->resolve('fiddlestick-service'));
    }

    /**
     * @covers Gimme\Resolver::pushResolver
     * @covers Gimme\Resolver::resolve
     */
    public function testResolverSimple()
    {
        $r = $this->getResolver();

        // mock resolver for fiddlesticks:
        $depResolver = function($d) {
            if($d == 'fiddlestick-service') {
                return true;
            }
        };

        $r->pushResolver($depResolver);
        $this->assertTrue($r->resolve('fiddlestick-service'));
    }

    /**
     * @expectedException Gimme\Exception\UnknownDependencyException
     * @covers Gimme\Resolver::pushResolver
     * @covers Gimme\Resolver::resolve
     */
    public function testResolverThrowsOnUnknownDependency()
    {
        $r = $this->getResolver();

        // mock resolver for fiddlesticks:
        $depResolver = function($d) {
            if($d == 'fiddlestick-service') {
                return true;
            }
        };

        $r->pushResolver($depResolver);
        $this->assertTrue($r->resolve('banana-service'));
    }

    /**
     * @covers Gimme\Resolver::pushResolver
     * @covers Gimme\Resolver::popResolver
     */
    public function testPushPopCallableResolver()
    {
        $r = $this->getResolver();

        $depResolver = function() { return 'fiddlesticks'; };

        $r->pushResolver($depResolver);
        $this->assertEquals($depResolver, $r->popResolver());

        // Nothing left to pop:
        $this->assertNull($r->popResolver());
    }

    /**
     * @covers Gimme\Resolver::pushResolver
     * @covers Gimme\Resolver::popResolver
     */
    public function testPushPopInstanceResolver()
    {
        $r = $this->getResolver();
        $m = $this->getDependencyResolver();

        $r->pushResolver($m);
        $this->assertNotNull($r->popResolver());
        $this->assertNull($r->popResolver());
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers Gimme\Resolver::pushResolver
     */
    public function testPushPopInvalidResolver()
    {
        $r = $this->getResolver();
        $r->pushResolver('baloney');
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers Gimme\Resolver::pushResolver
     */
    public function testPushPopInvalidInstanceResolver()
    {
        $r = $this->getResolver();
        $r->pushResolver(new \stdClass);
    }
}
