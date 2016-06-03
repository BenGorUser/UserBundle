# Doctrine ODM MongoDB

> CAUTION! The actual release of DoctrineMongoDBBundle is not compatible with PHP7

The [basic configuration](basic_configuration.md) chapter is made with Doctrine ORM persistence layer.
Apart of that, this bundle also supports MongoDB's Doctrine ODM. The [bundle documentation][1] is
very complete and self-explanatory but anyway, this section shows basic configuration to make work
properly.

Firstly, install the bundle with Composer
```shell
$ composer require doctrine/mongodb-odm-bundle
```
Next, register the annotations library by adding the following to the autoloader
(below the existing `AnnotationRegistry::registerLoader` line) in the `app/autoload.php` file
```php
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

AnnotationDriver::registerAnnotationClasses();
```
Then, enable the bundle in the `app/config/AppKernel.php`:
```php
<?php

public function registerBundles()
{
    $bundles = [
        // ...
        new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
        new BenGorUser\UserBundle\BenGorUserBundle(),
        // ...
    ];
}
```
To get started, you'll need some basic configuration that sets up the document manager. The
easiest way is to enable auto_mapping, which will activate the MongoDB ODM across your application:
```yml
doctrine_mongodb:
    connections:
        default:
            server: mongodb://localhost:27017
            options: {}
    default_database: symfony_db
    document_managers:
        default:
            auto_mapping: true
```
Instead of ORM that needs inside `src/AppBundle/Entity` directory, the MongoDB's Doctrine
ODM needs the models be inside `src/AppBundle/Document` folder.
```php
<?php

namespace AppBundle\Document;

use BenGorUser\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class User extends BaseUser
{
}
```
The `app/config/security.yml` has some minor changes, all of them related with persistence layer, of course:
```yml
security:
    encoders:
        AppBundle\Document\User: bcrypt
    providers:
        mongodb_provider:
            mongodb: { class: AppBundle\Document\User, property: email }
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            pattern: ^/user
            guard:
                authenticators:
                    - bengor_user.form_login_user_authenticator
            provider: mongodb_provider
            form_login:
                check_path: bengor_user_user_login_check
                login_path: bengor_user_user_login
                failure_path: bengor_user_user_login
            logout:
                path: bengor_user_user_logout
                target: /
    access_control:
        - { path: ^/user/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/sign-up, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/enable, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/, role: ROLE_USER }
```
Finally, **register the routes** and create the `success_redirection_route` in the same way that
explains the [basic configuration](basic_configuration.md) chapter.

[1]: http://symfony.com/doc/current/bundles/DoctrineMongoDBBundle/index.html
