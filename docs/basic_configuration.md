# Prerequisites
### Translations
If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.
```yml
# app/config/config.yml

framework:
    translator: { fallbacks: ["%locale%"] }
```

> For more information about translations, check
[Symfony documentation](https://symfony.com/doc/current/book/translation.html) and
[available BenGorUser translations](https://github.com/BenGorUser/UserBundle/tree/master/src/BenGorUser/UserBundle/Resources/translations).

# Getting started

By default we recommend the following installation that will add the following adapters to the user bundle.

* Routing: [SymfonyRouting](https://github.com/BenGorUser/SymfonyRoutingBridgeBundle)
* Security: [SymfonySecurity](https://github.com/BenGorUser/SymfonySecurityBridgeBundle)
* Ui: [Twig](https://github.com/BenGorUser/TwigBridgeBundle)
* Persistence: [DoctrineORM](https://github.com/BenGorUser/DoctrineORMBridgeBundle)
* Mailer: [SwiftMailer](https://github.com/BenGorUser/SwiftMailerBridgeBundle)
* Bus: [SimpleBus](https://github.com/BenGorUser/SimpleBusBridgeBundle)

```
{
    "require": {
        "bengor-user/user-bundle": "^0.7",

        "bengor-user/symfony-routing-bridge-bundle": "^1.0.1",
        "bengor-user/symfony-security-bridge-bundle": "^1.0.1",
        "bengor-user/twig-bridge-bundle": "^1.0.1",
        "bengor-user/doctrine-orm-bridge-bundle": "^1.1.0",
        "bengor-user/swift-mailer-bridge-bundle": "^1.0.1",
        "bengor-user/simple-bus-bridge-bundle": "^1.0.1"
    }
} 
```

> Some other adapters for [routing](adapters_routing.md), [security](adapters_security.md),
[ui](adapters_ui.md), [persistence](adapters_persistence.md), [mailers](adapters_mailers.md) and 
[buses](adapters_buses.md) are available.

To install the desired adapters and the bundle itself run the following in the project root:

```bash
$ composer update
```

> Make sure you have [composer](http://getcomposer.org) globally installed 

Once the bundle has been installed enable it in the AppKernel:

```php
// app/config/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...

        // Dependencies required by the bundle, keep the order.
        // First bridges and then the UserBundle
        
        // Bridges
        new BenGorUser\TwigBridgeBundle\TwigBridgeBundle(),
        new BenGorUser\SymfonyRoutingBridgeBundle\SymfonyRoutingBridgeBundle(),
        new BenGorUser\SymfonySecurityBridgeBundle\SymfonySecurityBridgeBundle(),
        new BenGorUser\SwiftMailerBridgeBundle\SwiftMailerBridgeBundle(),
        new BenGorUser\DoctrineORMBridgeBundle\DoctrineORMBridgeBundle(),
        new BenGorUser\SimpleBusBridgeBundle\SimpleBusBridgeBundle(),
        new BenGorUser\SimpleBusBridgeBundle\SimpleBusDoctrineORMBridgeBundle(),
        
        // User bundle
        new BenGorUser\UserBundle\BenGorUserBundle(),
        // ...
    ];
}
```

After that, you need to extend our `BenGorUser\User\Domain\Model\User` class in order to build the Doctrine mapping properly.
The following snippet is the minimum code that bundle needs to work.
```php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use BenGorUser\User\Domain\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bengor_user")
 */
class User extends BaseUser
{
}
```


Next, you have to configure the bundle to work with the specific needs of your application in the `config.yml`:
```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall: main
```

If you plan to implement a login system, you need to configure the `security.yml`:
```yml
# app/config/security.yml

security:
    encoders:
        AppBundle\Entity\User: bcrypt
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

This bundle has some basic actions such as login, logout and registration already implemented. Just add the following
to your `routing.yml`:

```yml
# app/config/routing.yml

ben_gor_user:
    resource: '@BenGorUserBundle/Resources/config/routing/all.yml'
```

It requires a route with its related controller action for `success_redirection_route`, so, the following code it can
be a plain and simple example for that.

```php
// src/AppBundle/Controller/DefaultController.php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/user/", name="bengor_user_user_homepage")
     */
    public function adminAction()
    {
        // ...
    }
}
```

> The list of available routes is available in the [routes by default doc](usage_routes_by_default.md). To customize
the routes check [extending routes doc](extending_customize_urls.md)

That's all! Now that the bundle is configured, the last thing you need to do is update your database:

```bash
$ bin/console doctrine:schema:update --force
```

With this basic configuration you have single user login, logout and registration without confirmation.

- For **multiple users** check [this guide](usage_multiple_users.md).
- In order to use **MongoDB's Doctrine ODM** as persistence layer follow [this chapter](doctrine_odm_mongodb.md).
- Back to the [index](index.md).
