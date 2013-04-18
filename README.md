# gimme

Inject your services with *magic*.

A super-simple library to auto-magically inject named services from a `$services` argument. Services
come from service providers, that can be introduced to Gimme through a simple stack layout.

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
    function($serviceName) { return $pimple[$serviceName]; };
);

// Use it:
$resolver->call(function($services = array('myService')) {
    var_dump($services->myService); #=> Foo
});
```
## Should I use this?

*Dunno*. It was fun to work on, but I'm not going to be the one to tell you to use it. The code
is solid, if there's a point in using it for your needs, it's a point for you to make.

## Features:

- Light-weight, tested code-base.
- Hands-off service injection through the $services argument
- Stupid-simple to implement support for any type of service provider
- Your callable/method's arguments are preserved, so it's a great fit with those modern space-age frameworks

    ```php
    // warning: example may not make much sense
    $app->get('/user/{id}', function($id, $services = array('user') {
        return $services->user->get($id);
    });
    ```

- Service providers may be a simple closure:

    ```php
    <?php

    $resolver->pushProvider(function($serviceName) {
        if($serviceName == 'bananas') {
            return 'Here, have some bananas!';
        }
    });
    ```

- Support for callable-binding (bind your callable to the method injector, call it whenever).

    ```php
    <?php

    $bound = $resolver->bind(function($services = array('someService')) {
        return $services->someService->doThings();
    });

    // Give the bound method to your killer router of sorts:

    ```

- Support for service-name aliases:

    ```php
    <?php

    $resolver->alias('bananaService', 'turnipService');
    $resolver->call(function($services = array('bananaService')) {
        print "I love {$services->bananaService->name()}!"; #=> I love turnip!
    });
    ```

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
