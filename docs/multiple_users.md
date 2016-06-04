#Multiple users

The [Getting started](getting_started.md) chapter only indicates how it registers your awesome user class. But this
bundle is more powerful that it would seem and it allows use more than one user class in your application. All this
bundle [services](service_reference.md) are register via PHP inside CompilerPass so, the Symfony dependency injection
container loads on the fly depending how many users are registered under `ben_gor_user` configuration section in
`app/config/config.yml`.

If your bundle configuration `user_class` looks like the following code snippet, the services are generated taking
only this user in mind. 
```yml
ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall: main
```
And for example if you execute the `bin/console debug:container | grep bengor_user.log_in_user`
you'll see the following:
```bash
bengor_user.log_in_user              BenGorUser\User\Application\Command\LogIn\LogInUserCommand
```
Otherwise, if your `user_class` contains multiple choices for example something like this
```yml
ben_gor_user:
    user_class:
        applicant:
            class: AppBundle\Entity\Applicant
            firewall: applicant
        employee:
            class: AppBundle\Entity\Employee
            firewall: employee
```
the above command will print the following:
```bash
bengor_user.log_in_applicant         BenGorUser\User\Application\Command\LogIn\LogInUserCommand
bengor_user.log_in_employee          BenGorUser\User\Application\Command\LogIn\LogInUserCommand
```


Furthermore, the basic security configuration that it is appear in the [Getting started](getting_started.md) chapter
can be modified to something like this, and it can be take advantage of the configuration to create different firewalls
for each user.
```yml
security:
    encoders:
        AppBundle\Entity\Applicant: bcrypt
        AppBundle\Entity\Employee: bcrypt
    providers:
        bengor_applicant:
            id: bengor_user.applicant_provider
        bengor_employee:
            id: bengor_user.employee_provider
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        applicant:
            anonymous: ~
            pattern: ^/applicant
            guard:
                authenticators:
                    - bengor_user.form_login_applicant_authenticator
            provider: bengor_applicant
            form_login:
                check_path: bengor_user_applicant_login_check
                login_path: bengor_user_applicant_login
                failure_path: bengor_user_applicant_login
            logout:
                path: bengor_user_applicant_logout
                target: /
        employee:
            anonymous: ~
            pattern: ^/employee
            guard:
                authenticators:
                    - bengor_user.form_login_employee_authenticator
            provider: bengor_employee
            form_login:
                check_path: bengor_user_employee_login_check
                login_path: bengor_user_employee_login
                failure_path: bengor_user_employee_login
            logout:
                path: bengor_user_employee_logout
                target: /
    access_control:
        - { path: ^/applicant/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/applicant/sign-up, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/applicant/enable, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/applicant/, role: ROLE_USER }
        
        - { path: ^/employee/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/employee/sign-up, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/employee/enable, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/employee/, role: ROLE_USER }
```
Finally, like in the [Getting started](getting_started.md) chapter you need routes after firewall
authorization so, something similar can be the minimal code snippet.
```php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/applicant/", name="bengor_user_employee_homepage")
     * @Route("/employee/", name="bengor_user_applicant_homepage")
     */
    public function adminAction()
    {
        // ...
    }
}
```
