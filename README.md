ServicesBundle
=============

This bundle implements a service to call rest api. Features include:
 - Call a rest API and receive a json decoded to array
 - Call a rest API and receive a json not decoded
 - Automatically you can pass as parameter the http verb and json, it automatically will make the request

Note
----

The bundle is released and can be used. However it under heavy development.

Documentation
-------------

This bundle permit to call rest api and offer an entity with base configuration to return a json.

To use this bundle, first you must use the service with DI like this:

```php
$apiRest = $this->get('services.chiamatarest');
```

Then you can do your settings to the service. 
By default the service will search, in the json response from rest api, for a field named "message" that contain the result message of the call and for a field name "success" for the result (true or false) of the call.
If this doesn't meet you, you can change thei name in this way:

For the message field:
```php
$apiRest->setNomeCampoMessage("<your-field-name>");
```

For the success field:
```php
$apiRest->setNomeCampoSuccess("<your-field-name>");
```

You can also decide to not make the test for a specific field setting this option:
```php
$apiRest->setControlSuccess(false);
```
By default it's true.

Then, you can make other settings, like this for example:

Setting the project that is calling, for the logs:
```php
$apiRest->setChiamante("<your-application>");
```

Set your http verb
```php
$apiRest->setTipoChiamata("<http-verb");
```

Set the url to call
```php
$apiRest->setUrl("<api-rest-url>");
```

You can pass a json input, for now only POST, PUT and GET http verb accept json input. You can do it so:
```php
$apiRest->setJson("<your-json>");
```

For each request the service will test the returning http code. If it receives 200,201 or 202 it's all ok.
In other case it will raise an exception that needs to be captured.

You can now make the api request receiving an array like this
```php
$returnJsonAarray=$apiRest->chiamataRestDecodificata();
```

Or You can make the api request receiving only the json like this
```php
$returnJson=$apiRest->chiamataRest();
```

You can however see what http code the request returned in this way:
```php
$returnHttpCode=$apiRest->getHttpcode();
```


Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require brunopicci/call-rest-api
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Services\Bundle\Rest\ServicesRestBundle(),
        );

        // ...
    }

    // ...
}
```

In config.yml import services.yml of the bundle:
imports:
    ...
    - { resource: "@ServicesRestBundle/Resources/config/services.yml" }


License
-------

This bundle is under the MIT license.
