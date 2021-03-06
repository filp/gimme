# gimme [![Build Status](https://travis-ci.org/filp/gimme.png?branch=master)](https://travis-ci.org/filp/gimme)

Inject your services with *magic*.

A super-simple library to auto-magically inject named services from a `$services`\* argument. Services
come from service providers, that can be introduced to Gimme through a simple stack layout.

\* Or not, have a look at `Gimme\Resolver::setArgumentName`

```php
<?php

use Gimme\Resolver;
use Pimple;
use Foo;

// Create a service provider, for this example, using Pimple:
$pimple = new Pimple;
$pimple['myService'] = function() {
    return new Foo;
};

// Introduce the provider to Gimme, wrapping it in a closure
// that knows how to talk to Pimple:
$resolver = new Resolver;
$resolver->pushProvider(
    function($serviceName) use($pimple) { return $pimple[$serviceName]; };
);

// Use it. Tell Gimme that this callable (which can be a closure, a function
// or a method) wants to use a service called 'myService', which one of the
// registered service providers will be able to fetch for you.
$resolver->call(function($services = array('myService')) {
    var_dump($services->myService); #=> instance of Foo
});
```

## Features:

- Light-weight, tested code-base.
- Hands-off service injection through the $services argument
- Stupid-simple to implement support for any type of service provider
- Your callable/method's arguments are preserved, so it's a great fit with those modern space-age frameworks

    ```php
    // warning: example may not make much sense
    $app->get('/user/{id}', $resolver->bind(function($id, $services = array('user') {
        return $services->user->get($id);
    }));
    ```

- Service providers may be a simple closure:

    ```php
    $resolver->pushProvider(function($serviceName) {
        if($serviceName == 'bananas') {
            return 'Here, have some bananas!';
        }
    });
    ```

- Support for callable-binding (bind your callable to the method injector, call it whenever).

    ```php
    $bound = $resolver->bind(function($services = array('someService')) {
        return $services->someService->doThings();
    });

    // Give the bound method to your killer router of sorts:
    $app->get('/foo', $bound);
    ```

- Support for service-name aliases:

    ```php
    $resolver->alias('bananaService', 'turnipService');
    $resolver->call(function($services = array('bananaService')) {
        print "I love {$services->bananaService->name()}!"; #=> I love turnip!
    });
    ```

## Should I use this?

*Dunno*. It was fun to work on, but I'm not going to be the one to tell you to use it. The code
is solid, if there's a point in using it for your needs, it's a point for you to make.

## Install it:

Use [Composer](http://getcomposer.org), add the following to your `composer.json`:

```json
{
    "require": {
        "filp/gimme": "dev-master"
    }
}
```

And then:

```bash
$ composer install
```
