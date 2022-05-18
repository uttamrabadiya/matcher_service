

## REO Matcher Microservice

This application is built with using Laravel 8

#### Initial Commands to setup project:
* Clone this repository.
* Install composer packages using `composer install` command.
* Copy **.env.example** file to **.env** file.
* Setup database credentials and run `php artisan migrate` command.
* Repository also have demo database from Seeder, so run `php artisan db:seed` command for sample data of Properties & SearchProfiles.


This application serves two routes as shown below:

**/** this will give a list of properties from seed database.

**/api/match/{property-uuid}** this endpoint will provide a list of matched profiles by selected property.
