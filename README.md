# soarce/client [![Packagist](https://img.shields.io/packagist/dt/soarce/client.svg)](https://packagist.org/packages/soarce/client)

## Version: 0.3.0

## Overview

This package is the client part of SOARCE - a tool for collecting, reading and analyzing PHP code coverage
withing a service oriented architecture / microservice environment. It has to be installed per service as a
dev requirement. This library will intercept certain calls to the service/application and either execute
necessary code for collecting and storing coverage before/after the actual application code or execute it's
own actions instead (e.g. handing out coverage data to the master application).

### DO NOT USE IN PRODUCTION!

Althought there will be security measures in place, include this library only as a --dev dependency. The
intended use is local development or use within a CI/CD pipeline. See documentation for more advanced examples.

## Installation

composer.json:
```json
{
  "require-dev": {
    "soarce/client": "*"
  }
}
```

or preferably run this composer command in your project root:
```
$ composer require --dev --prefer-dist "soarce/client"
```

In addition, you will have to install and enable xdebug in order to generate code coverage. It has not been
included in the "required" section as it would disallow composer-runs without it. 

## Configuration

### ENV-Variables

* string `SOARCE_ACTION_PARAM_NAME` = "SOARCE": names the SOARCE interceptor param name. Use something long and
random to obfuscate an active SOARCE client if necessary and/or to solve parameter name conflicts with your
application. It has to match the main application's parameter name setting.
* string `SOARCE_APPLICATION_NAME` = $_SERVER['HOSTNAME']: names the server/application for gathering the stats.
This should be the same name used in the main application's config. As a fallback the "HOSTNAME" server variable
will be used - with docker this means defaulting to the docker container's id.
* string `SOARCE_DATA_PATH` = "/tmp/": any writable location on your server / in your container. Named pipes, 
trigger and pid-files will be written there. If you host multiple services from the same host or container,
make sure they use different `SOARCE_DATA_PATH`s. Coverage is sent directly to the master application,
trace is written to the named pipes, parsed in memory and the result then sent to the master.

### X-Debug
```
xdebug.auto_trace = 0
xdebug.trace_format = 1
xdebug.trace_enable_trigger = 0
```

This is counterintuitive, but, SOARCE triggers coverage and tracing itself. 

### docker-compose

Currently the client expects a few preconditions at static hostnames/addresses - we plan to add configuration
options later:
* the main application will be expected at the address "http://soarce.local:80/"
* the redis server (for reliable mutex locking of the pipes) at "tcp://soarce.local:6379"
* clone the application [soarce/application](https://gitlab.home.segnitz.net/soarce/application) and run
  `docker-compose up` for it, it will create and run the necessary services within a virtual network.
* make sure that the containers you install this package to can access the aforementioned services. This can be
  achieved for example by running them with docker-compose and putting them into the same virtual network:
  * for your application container (e.g. php-fpm or apache + mod_php) add a new network to the current one so it
    looks for example like this:
    ```
    services:
      my-app:
        build: [...]
        volumes: [...]
        links: [...]
        networks:
          default:
            aliases:
              - my-app.local
          soarce_default:
    ```
  * define the network `soarce_default` as an external network by adding this to the end of your docker-compose.yml:
    ```
    networks:
      soarce_default:
        external: true
    ```

## Debug Interface

Just call the index page of your application - e.g. `/` or `/index.php` and add `?SOARCE=index` to the call.

## Known Issues

### Security
* Currently, nothing prevents anybody from accessing the SOARCE functionality apart from parameter obfuscation,
see roadmap for planned countermeasures.
* Component requires xdebug to be active 

### Separating Requests
We plan to group requests which are passed on further to subsequent services by the topmost request
to the initial application/service - and we'll aim for working them up. This will require passing on
request IDs manually through the respective SDKs or adaptors. We'll provide functionality to help with
that task.
