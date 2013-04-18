<?php
/**
 * Gimme - inject your dependencies and junk through
 * reflection  *~ magic ~*.
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Gimme;
use Gimme\TestCase;
use Gimme\Exception\UnknownServiceException;
use InvalidArgumentException;

class ResolverTest extends TestCase
{

    /**
     * @covers Gimme\Resolver::pushProvider
     * @covers Gimme\Resolver::alias
     * @covers Gimme\Resolver::resolve
     */
    public function testServiceAlias()
    {
        $r = $this->getResolver();

        $provider = $this->getServiceProviderCallable('fiddlestick-service');

        $r->pushProvider($provider);
        $r->alias('foo', 'fiddlestick-service');

        $this->assertTrue($r->resolve('foo'));
        $this->assertTrue($r->resolve('fiddlestick-service'));
    }

    /**
     * @covers Gimme\Resolver::pushProvider
     * @covers Gimme\Resolver::resolve
     */
    public function testResolveService()
    {
        $r = $this->getResolver();

        $provider = $this->getServiceProviderCallable('fiddlestick-service');

        $r->pushProvider($provider);
        $this->assertTrue($r->resolve('fiddlestick-service'));
    }

    /**
     * @expectedException Gimme\Exception\UnknownServiceException
     * @covers Gimme\Resolver::pushProvider
     * @covers Gimme\Resolver::resolve
     * @covers Gimme\Resolver::throwOnMissingService
     */
    public function testResolverThrowsOnUnknownService()
    {
        $r = $this->getResolver();
        $r->throwOnMissingService(true);

        $provider = $this->getServiceProviderCallable('fiddlestick-service');

        $r->pushProvider($provider);
        $this->assertTrue($r->resolve('banana-service'));
    }

    /**
     * @covers Gimme\Resolver::pushProvider
     * @covers Gimme\Resolver::resolve
     * @covers Gimme\Resolver::throwOnMissingService
     */
    public function testResolverDoesNotThrowOnMissingService()
    {
        $r = $this->getResolver();

        $provider = $this->getServiceProviderCallable('fiddlestick-service');

        $r->pushProvider($provider);
        $this->assertFalse($r->resolve('banana-service'));
    }

    /**
     * @covers Gimme\Resolver::pushProvider
     * @covers Gimme\Resolver::popProvider
     */
    public function testPushPopProviderCallable()
    {
        $r = $this->getResolver();

        $provider = $this->getServiceProviderCallable('fiddlestick-service');

        $r->pushProvider($provider);
        $this->assertEquals($provider, $r->popProvider());

        // Nothing left to pop:
        $this->assertNull($r->popProvider());
    }

    /**
     * @covers Gimme\Resolver::pushProvider
     * @covers Gimme\Resolver::popProvider
     */
    public function testPushPopProviderInstance()
    {
        $r = $this->getResolver();
        $provider = $this->getServiceProvider();

        $r->pushProvider($provider);
        $this->assertEquals($provider, $r->popProvider());
        $this->assertNull($r->popProvider());
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers Gimme\Resolver::pushProvider
     */
    public function testPushPopInvalidResolver()
    {
        $r = $this->getResolver();
        $r->pushProvider('baloney');
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers Gimme\Resolver::pushProvider
     */
    public function testPushPopInvalidInstanceResolver()
    {
        $r = $this->getResolver();
        $r->pushProvider(new \stdClass);
    }
}
