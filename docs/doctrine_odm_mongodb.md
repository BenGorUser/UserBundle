# Doctrine ODM MongoDB

> CAUTION! The actual release of DoctrineMongoDBBundle is not compatible with PHP7

The [basic configuration](basic_configuration.md) chapter is made with Doctrine ORM persistence layer.
Apart of that, this bundle also supports MongoDB's Doctrine ODM. The [bundle documentation][1] is
very complete and self-explanatory but anyway, this section shows basic configuration to make work
properly.

Firstly, install the bundle with Composer
```shell
$ composer require bengor-user/doctrine-odm-mongodb-bridge-bundle
```
Next, register the annotations library by adding the following to the autoloader
(below the existing `AnnotationRegistry::registerLoader` line) in the `app/autoload.php` file
```php
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

AnnotationDriver::registerAnnotationClasses();
```
Once the bundle has been installed enable it in the AppKernel:
```php
// app/config/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
        // ...
        new BenGorUser\DoctrineODMMongoDBBridgeBundle\DoctrineODMMongoDBBridgeBundle(),  
        
        // In case we are using SimpleBus as Bus adapter
        new BenGorUser\SimpleBusBridgeBundle\SimpleBusBridgeBundle(),
        new BenGorUser\SimpleBusBridgeBundle\SimpleBusDoctrineODMMongoDBBridgeBundle(),
        
        new BenGorUser\UserBundle\BenGorUserBundle(),
        // ...
    ];
}
```
Instead of ORM that needs inside `src/AppBundle/Entity` directory, the MongoDB's Doctrine ODM needs the models be
inside `src/AppBundle/Document` folder.
```php
// src/AppBundle/Document/User.php

namespace AppBundle\Document;

use BenGorUser\User\Domain\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class User extends BaseUser
{
}
```
To get started, you'll need some basic configuration that sets up the document manager. The easiest way is to enable
`auto_mapping`, which will activate the MongoDB ODM across your application:
```yml
# app/config/config.yml

doctrine_mongodb:
    connections:
        default:
            server: mongodb://localhost:27017
            options: {}
    default_database: bengor_user_db
    document_managers:
        default:
            auto_mapping: true

ben_gor_user:
    user_class:
        user:
            class: AppBundle\Document\User
            persistence: doctrine_odm_mongodb
            firewall: main
```

The `security.yml` file is the same that the basic configuration but you need to change FQCN of User class in the
`encoders` section:
```yml
# app/config/security.yml

security:
    encoders:
        AppBundle\Document\User: bcrypt
    providers:
        bengor_user:
            id: bengor_user.user.provider
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            pattern: ^/user
            guard:
                authenticators:
                    - bengor_user.user.form_login_authenticator
            provider: bengor_user
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
All about **routes** work in the same way that explains in the [basic configuration](basic_configuration.md)
chapter.

- Back to the [index](index.md).

[1]: http://symfony.com/doc/current/bundles/DoctrineMongoDBBundle/index.html
