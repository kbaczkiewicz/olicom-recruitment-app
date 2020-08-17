# Olicom recruitment app

To start app open console, go into _phpdocker_ folder and type: `docker-compose up -d`.

## In-app commands

To get into app container, type `docker-compose exec php-fpm bash`. Then, in order to be able to run commands, `cd app` will get you into application's source folder inside container.

Data is presented in JSON format.

##### Get user info command

Type `github:user:get <username>` to get user data.

##### Get repository info command

Type `github:repo:get <owner> <repository>` to get repository data.

## App routes

After running app, two routes will be available. That is:

`localhost:8080/users/{login}`

which will retrieve information about user, and

`localhost:8080/repositories/{ownerlogin}/{repositoryname}`, which will retrieve info about repository.

## Running tests

To run tests, inside _app_ folder of container, type `php bin/phpunit tests`, which will run all of project's tests.
