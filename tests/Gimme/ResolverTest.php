<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme;
use Gimme\TestCase;
use InvalidArgumentException;

class ResolverTest extends TestCase
{
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
