# Multiple users

In case you need more than one user type you need to go across the following steps that are very similar to those you
followed in [basic configuration](basic_configuration.md) chapter.

First of all you need to define a new user model.

```php
// src/AppBundle/Entity/Employee.php

namespace AppBundle\Entity;

use BenGorUser\User\Domain\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="employee")
 */
class Employee extends BaseUser
{
}
```

Next, you have to append the newly created class' definition to the one you created in the 
[basic configuration](basic_configuration.md) chapter:
```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall: user
        employee:
            class: AppBundle\Entity\Employee
            firewall: employee
```

> **IMPORTANT!** `your_user_type` mentioned across all the docs refers to the name you asign to each user model. The 
array key bellow user_class will be used for that. In this case `user` and `employee` strings will be used to replace 
`your_user_type` in the examples you will find.
 
After that, the basic security configuration needs to be changed. You will need to add a custom encoder, provider, 
firewall and access control for your newly created user type. You will end up with something similar to this:

```yml
security:
    encoders:
        AppBundle\Entity\User: bcrypt
        AppBundle\Entity\Employee: bcrypt
    providers:
        chain_provider:
            chain:
                providers: [bengor_user, bengor_employee]
        bengor_user:
            id: bengor_user.user.provider
        bengor_employee:
            id: bengor_user.employee.provider
    firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false
            user:
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
            employee:
                anonymous: ~
                pattern: ^/employee
                guard:
                    authenticators:
                        - bengor_user.employee.form_login_authenticator
                provider: bengor_employee
                form_login:
                    check_path: bengor_user_employee_login_check
                    login_path: bengor_user_employee_login
                    failure_path: bengor_user_employee_login
                logout:
                    path: bengor_user_employee_logout
                    target: /
    access_control:
        - { path: ^/user/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/sign-up, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/enable, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/, role: ROLE_USER }
        
        - { path: ^/employee/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/employee/sign-up, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/employee/enable, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/employee/, role: ROLE_USER }
```

> For further info about security configuration check [oficial Symfony docs](http://symfony.com/doc/current/book/security.html)

Finally, you need to define the destination action the user will be redirected after login in as you did in 
[basic configuration chapter](basic_configuration.md) .

```php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/user/", name="bengor_user_user_homepage")
     */
    public function userHomeAction()
    {
        // ...
    }
    
    /**
     * @Route("/employee/", name="bengor_user_employee_homepage")
     */
    public function employeeHomeAction()
    {
        // ....
    }
}
```

> You can customize the routes naming following [Customize URLs chapter](extending_customize_urls.md)

- Back to the [index](index.md).
