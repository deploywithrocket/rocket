# What is Rocket?

Rocket is a free, open source and self-hostable deployment tool.
Commit, push, your project is deployed! Rocket takes care of the deployment process on your server.
It can deploy any PHP or Javascript application, regardless of the framework, with or without dependencies or assets to build.

* One time setup process
* Built with modern technologies
* Elegant beautiful interface
* Zero downtime deployments
* Agentless, it’s just SSH
* Integrates with GitHub with public or private repositories
* Shared directories and `cron` jobs sync
* Simultaneous deployments (queues)

## Understanding the need

Code deployments are a hassle. You have to manually pull in your code, install dependencies, build assets, run migrations, and finally bring your application back online. This is a lot of manual work and can be error-prone.

::: details Example of a Laravel deployment
You're supposed to put your application into maintenance mode before deploying with `php artisan down`, pull in your code, clear the cache with `php artisan cache:clear`, install dependencies with `composer install` and `npm install`, then build your assets with `npm run production`, run migrations with `php artisan migrate`, and finally bring your application back online with `php artisan up`. Remember to restart `php-fpm` and `nginx` after the deployment, and queue workers with `php artisan queue:restart` if you use them.

This is just a basic example, but you get the idea.
:::

Tools like [Deployer](https://deployer.org/) can automate deployment scenarios and run from your local environnement. To work successfully they need a configuration and have to be ran manually everytime you want to deploy your website.

On the other hand, there are paid tools like [Envoyer.io](https://envoyer.io) that offer a higher level of automation by performing the deployment online, in a completely automated way for each push you do. Problem, these services are not free, or some with a free-tier which limits the number of projects.

Rocket provides the benefits from both tools without the cons.
