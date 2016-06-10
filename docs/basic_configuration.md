# Prerequisites
### Translations
If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.
```yml
# app/config/config.yml

framework:
    translator: { fallbacks: ["%locale%"] }
```
For more information about translations, check [Symfony documentation][1].

# Getting started

The first step is enable the bundle in the `app/config/AppKernel.php`:
```php
<?php

public function registerBundles()
{
    $bundles = [
        // ...

        // Dependencies required by the bundle, keep the order.
        // First bridges and then the UserBundle
        
        // Bridges
        new BenGorUser\DoctrineORMBridgeBundle\DoctrineORMBridgeBundle(),
        new BenGorUser\TwigBridgeBundle\TwigBridgeBundle(),
        new BenGorUser\SwiftMailerBridgeBundle\SwiftMailerBridgeBundle(),
        new BenGorUser\SymfonyRoutingBridgeBundle\SymfonyRoutingBridgeBundle(),
        new BenGorUser\SymfonySecurityBridgeBundle\SymfonySecurityBridgeBundle(),
        new BenGorUser\SimpleBusBridgeBundle\SimpleBusBridgeBundle(),
        
        // User bundle
        new BenGorUser\UserBundle\BenGorUserBundle(),
        // ...
    ];
}
```

After that, you need to extend our `BenGorUser\User\Domain\Model\User` class in order to build the Doctrine mapping properly.
The following snippet is the minimum code that bundle needs to work.
```php
<?php

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


Next, you have to configure the bundle to work with the specific needs of your application inside
`app/config/config.yml`:
```yml
ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall: main
```

If you plan to implement a login system, you need to configure the `app/config/security.yml`:
```yml
security:
    encoders:
        AppBundle\Entity\User: bcrypt
    providers:
        bengor_user:
            id: bengor_user.user_provider
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
to your `app/config/routing.yml`:
```yml
ben_gor_user:
    resource: '@BenGorUserBundle/Resources/config/routing/all.yml'
```

It requires a route with its related controller action for `success_redirection_route`, so, the following code it can
be a plain and simple example for that.

```php
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

> You can change base route names following 

That's all! Now that the bundle is configured, the last thing you need to do is update your database:
```bash
$ bin/console doctrine:schema:update --force
```

With this basic configuration you have single user login, logout and registration without confirmation.

- For **multiple users** check [this guide](usage_multiple_users.md).
- In order to use **MongoDB's Doctrine ODM** as persistence layer follow [this chapter](doctrine_odm_mongodb.md).
- Back to the [index](index.md).

[1]: https://symfony.com/doc/current/book/translation.html
