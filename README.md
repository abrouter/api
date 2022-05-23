# ABRouter API

Please, feel free to visit the [full documentation](https://docs.abrouter.com/docs/intro/).

If you're wondering to deploy the ABRouter locally, please see the README of [main ABRouter repository](https://github.com/abrouter/abrouter). 

ABRouter API is based on Laravel, used Redis, MySQL.

## Modules

### ABRouter
Responsible for the all features of ABRouter: experiments, feature flags, statistics.

### Auth
Responsible for user authorization: login, auth, jwt handling.


### Core
Code to re-use in the other packages.


## Tests running

To run tests use the following command:

```
make test-run
```

This command will switch the database and run tests in the container with the same host.

## Contributing

Feel free to contibute. 
