# soarce/client

## Version: 0.0.2

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

## Configuration

... TBD/TBA ...

## Debug Interface

Just call the index page of your application - e.g. `/` or `/index.php` and add `?SOARCE=index` to the call.

## Known Issues

### Security
* Currently, nothing prevents anybody from accessing the SOARCE functionality, see roadmap for planned
  countermeasures
* Component requires xdebug to be active 

### Performance
Expect your test suite to run a bit slower than before and/or use more of resources. How much strongly
depends on the software you're testing.
* component needs xdebug to be active which will impact performance by itself
* generating coverage is write-heavy
* collecting data and storing it is heavy on network (usually between local containers) and also CPU

### Separating Requests
We plan to group requests which are passed on further to subsequent services by the topmost request
to the initial application/service - and we'll aim for working them up. This will require passing on
request IDs manually through the respective SDKs or adaptors. We'll provide functionality to help with
that task.
