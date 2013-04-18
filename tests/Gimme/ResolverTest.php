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
     * @expectedException InvalidArgumentException
     * @covers Gimme\Resolver::bind
     */
    public function testThrowsOnInvalidCallable()
    {
        $this->getResolver()->bind('where do seagulls go at night');
    }

    /**
     * @covers Gimme\Resolver::bind
     * @covers Gimme\Resolver::pushProvider
     */
    public function testBindSimple()
    {
        $r = $this->getResolver();
        $r->pushProvider($this->getServiceProviderCallable('yayProvider'));

        $bound = $r->bind(function($id, $services = array('yayProvider')) {
            $this->assertTrue($services->yayProvider);
            $this->assertEquals(10, $id);
        });

        $id = 10;
        call_user_func($bound, $id);
    }

    /**
     * @covers Gimme\Resolver::bind
     * @covers Gimme\Resolver::pushProvider
     */
    public function testBindSimpleWithMissingService()
    {
        $r = $this->getResolver();
        $r->pushProvider($this->getServiceProviderCallable('yayProvider'));

        $bound = $r->bind(function($services = array('yayProvider', 'bananaProvider')) {
            $this->assertTrue($services->yayProvider);
            $this->assertNull($services->bananaProvider);
        });

        call_user_func($bound);
    }

    /**
     * @covers Gimme\Resolver::bind
     * @covers Gimme\Resolver::pushProvider
     */
    public function testBindSimpleWithAdditionalArguments()
    {
        $r = $this->getResolver();
        $r->pushProvider($this->getServiceProviderCallable('yayProvider'));

        $bound = $r->bind(function($id, $services = array('yayProvider')) {
            $this->assertTrue($services->yayProvider);
            $this->assertEquals(10, $id);

            // Verify additional arguments passed to the callable:
            $args = func_get_args();
            $this->assertEquals($args[2], 22);
            $this->assertEquals($args[3], "bananas");
            $this->assertEquals($args[4], "lionel ritchie");
        });

        $id = 10;
        call_user_func($bound, $id, 22, "bananas", "lionel ritchie");
    }


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
        $this->assertNull($r->resolve('banana-service'));
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
