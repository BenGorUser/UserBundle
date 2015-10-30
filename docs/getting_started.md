##Getting started

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

use BenGor\User\Domain\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bengor_user")
 */
class User extends BaseUser
{
}
```

That's all! Now that the bundle is configured, the last thing you need to do is update your database schema because
you have added your new awesome user class.
```bash
$ php app/console doctrine:schema:update --force
```
