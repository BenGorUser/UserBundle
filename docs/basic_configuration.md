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

After that, you need to extend our `BenGor\UserBundle\Model\User` class in order to build the Doctrine mapping properly.
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


Next, you have to configure the bundle to work with the specific needs of your application inside
`app/config/config.yml`:
```yml
ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall:
                name: main
            registration: ~
```

If you plan to implement a login system, you need to configure the `app/config/security.yml`:
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

This bundle has some basic actions such as login, logout and registration already implemented. Just add the following
to your `app/config/routing.yml`:
```yml
ben_gor_user:
    resource: '@BenGorUserBundle/Resources/config/routing/all.yml'
```

That's all! Now that the bundle is configured, the last thing you need to do is update your database:
```bash
$ bin/console doctrine:schema:update --force
```

With this basic configuration you have single user login, logout and registration without confirmation.

- For multiple users check [this guide](multiple_users.md).
- In case you one to send invitation emails to users to join your app follow [this guide](invitation_system.md).

