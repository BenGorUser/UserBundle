#Getting started

The first step is enable the bundle in the `app/config/AppKernel.php`:
```php
<?php

public function registerBundles()
{
    $bundles = [
        // ...
        new BenGor\UserBundle\BenGorUserBundle(),
        // ...
    ];
}
```

> Please, keep in mind that *BenGorUser* is built with **Domain-Driven design** approach so the directory structure
does not correspond with the Symfony standard structure and the **`doctrine-bundle`'s auto-mapping does not work
properly with this bundle**.

So, to avoid any problem related with the above reminder you have to disabled the auto-mapping inside doctrine section
of `app/config/config.yml` file and added mappings manually. The following code is the minimum needed to work in the
Symfony standard edition.
```yml
doctrine:
    (...)

    orm:
        auto_generate_proxy_classes: "%kernel.debug%"
        naming_strategy: doctrine.orm.naming_strategy.underscore
        mappings:
            BenGorUser:
                mapping: true
                type: yml
                dir: "%kernel.root_dir%/../vendor/bengor/user-bundle/src/Resources/config/doctrine"
                prefix: 'BenGor\User\Domain\Model'
                is_bundle: false
            AppBundle:
                mapping: true
                type: annotation
                prefix: 'AppBundle\Entity'
                is_bundle: true
```

After that, you need to extend our `BenGor\Domain\Model\User` class in order to build the Doctrine mapping properly.
The following snippet is the minimum code that bundle needs to work.
```php
<?php

namespace AppBundle\Entity;

use BenGor\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bengor_user")
 */
class User extends BaseUser
{
}
```
In case if you want to use registration by invitation system you have to extend our `BenGor\Domain\Model\UserGuest`
for the same reason of User itself.
```php
<?php

namespace AppBundle\Entity;

use BenGor\User\Domain\Model\UserGuest as BaseUserGuest;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bengor_user_guest")
 */
class UserGuest extends BaseUserGuest
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
            firewall:
                name: main
```
This bundles comes with some defined routes, you have to enable adding the following lines in your
`app/config/routing.yml`:
```yml
ben_gor_user:
    resource: '@BenGorUserBundle/Resources/config/routing/all.yml'
```
If ypu plan to implement a login system, you need to configure the `app/config/security.yml`:
```yml
security:
    encoders:
        AppBundle\Entity\User: bcrypt
    providers:
        database_users:
            entity: { class: AppBundle:User, property: email }
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: ~
            guard:
                authenticators:
                    - bengor.user_bundle.security.form_login_user_authenticator
            provider: database_users
            logout:
                path: bengor_user_security_logout
                target: /
    access_control:
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
```

That's all! Now that the bundle is configured, the last thing you need to do is update your database schema because
you have added your new awesome user class.
```bash
$ bin/console doctrine:schema:update --force
```
