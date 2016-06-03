#Invitation system

BengorUserBundle has built in invitation management. In case you want to register new users by sending them an email
with a token, this is your use case. Just need to follow to simple steps:

You need to create an entity that extends our `BenGorUser\User\Domain\Model\UserGuest`, where required token will be stored.
```php
<?php

namespace AppBundle\Entity;

use BenGorUser\User\Domain\Model\UserGuest as BaseUserGuest;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bengor_user_guest")
 */
class UserGuest extends BaseUserGuest
{
}
```

And change the config you have created following [getting started](getting_started.md) guide, replacing 
`sign_up` option as follows:
```yml
ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall:
                name: main
            use_cases:
                sign_up:
                    type: by_invitation
```


