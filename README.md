# soarce/client [![Packagist](https://img.shields.io/packagist/dt/soarce/client.svg)](https://packagist.org/packages/soarce/client)

## Version: 0.7.0

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
* string `SOARCE_WHITELISTED_HOST_IPS` = "": a comma-separated list of IPv4 and IPv6 addresses that should be
able to access SOARCE commands and resources through this plugin. The empty default means no whitelisting active
and permits all requests. This is the default as SOARCE is a development tool and should not be accessible from
public networks anyways.
* string `SOARCE_WHITELISTED_PATHS` = "": a PATH_SEPARATOR (:) separated list of paths out of which SOARCE is
allowed to handout sourcecode on request - to display in code coverage views. You should include all possible
source code and library paths - a good start is usually the `common_path` parameter in the application's config,
for example "/var/www". As with the IP whitelist, an empty path whitelist disables the feature as SOARCE should
only be used in closed environments.
* string `SOARCE_PRESHARED_SECRET` = "": an arbitrary string which - if used - has to be identical to the
respective config key in the application's config. It is being sent as a HTTP header to effectively reduce
drive-by or XSS attacks as well as brute-force attempts to guess how to access SOARCE on a certain system.

### X-Debug
```
xdebug.auto_trace = 0
xdebug.trace_format = 1
xdebug.trace_enable_trigger = 0
```

This might be counterintuitive, but, SOARCE triggers coverage and tracing itself. 

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

## Known Issues

### Security
* Component requires xdebug to be active 

### Separating Requests
For within a service architecture we just added a feature that automatically detects the request-id of the parent request and the sequence number of child requests within that parent request.
This currently only works in it's zero-conf mode when each service is running in it's own VM or docker container - having a separate IP address - and without having gaps inbetween - like loadbalancers or services not equipped with an active SOARCE client. We'll add additional support options in the future.
