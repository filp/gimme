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
        $self = $this;

        $r = $this->getResolver();
        $r->pushProvider(function($service) {
            if($service == 'test') {
                return 'oh snap';
            }
        });

        $bound = $r->bind(function($test) use($self) {
            $self->assertEquals('oh snap', $test);
        });

        call_user_func($bound);
    }

    /**
     * @covers Gimme\Resolver::bind
     * @covers Gimme\Resolver::pushProvider
     */
    public function testBindSimpleWithMissingService()
    {
        $self = $this;

        $r = $this->getResolver();
        $provider = $this->getServiceProviderCallable('test');
        $r->pushProvider($provider);

        $bound = $r->bind(function($test, $nothing) use($self) {
            $self->assertTrue($test);
            $self->assertNull($nothing);
        });

        call_user_func($bound);
    }

    /**
     * @covers Gimme\Resolver::bind
     * @covers Gimme\Resolver::pushProvider
     */
    public function testBindSimpleWithAdditionalArguments()
    {
        $self = $this;

        $r = $this->getResolver();
        $provider = $this->getServiceProviderCallable('test');
        $r->pushProvider($provider);

        $bound = $r->bind(function($test, $nothing) use($self) {
            $self->assertTrue($test);
            $self->assertNull($nothing);
            $self->assertEquals(3, func_num_args());

            $args = func_get_args();
            $self->assertEquals('Hello!', end($args));
        });

        call_user_func($bound, 'Hello!');
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
