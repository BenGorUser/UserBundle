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
            firewall:
                name: main
```
And for example if you execute the `bin/console debug:container | grep bengor.user.application.service.log_in`
you'll see the following:
```bash
bengor.user.application.service.log_in_user              BenGor\User\Application\Service\LogOutUserService
```
Otherwise, if your `user_class` contains multiple choices for example something like this
```yml
ben_gor_user:
    user_class:
        applicant:
            class: AppBundle\Entity\Applicant
            firewall:
                name: applicant
        employee:
            class: AppBundle\Entity\Employee
            firewall:
                name: employee
```
the above command will print the following:
```bash
bengor.user.application.service.log_in_applicant         BenGor\User\Application\Service\LogOutUserService
bengor.user.application.service.log_in_employee          BenGor\User\Application\Service\LogOutUserService
```


Furthermore, the basic security configuration that it is appear in the [Getting started](getting_started.md) chapter
can be modified to something like this, and it can be take advantage of the configuration to create different firewalls
for each user.
```yml
security:
    encoders:
        AppBundle\Entity\Employee: bcrypt
        AppBundle\Entity\Applicant: bcrypt
    providers:
        database_employees:
            entity: { class: AppBundle:Employee, property: email }
        database_applicants:
            entity: { class: AppBundle:Applicant, property: email }
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        applicant:
            anonymous: ~
            guard:
                authenticators:
                    - bengor.user_bundle.security.form_login_applicant_authenticator
            provider: database_applicants
            form_login:
                check_path: bengor_user_applicant_security_login_check
                login_path: bengor_user_applicant_security_login
                failure_path: bengor_user_applicant_security_login
            logout:
                path: bengor_user_applicant_security_logout
                target: /
        employee:
            anonymous: ~
            pattern: ^/admin
            guard:
                authenticators:
                    - bengor.user_bundle.security.form_login_employee_authenticator
            provider: database_employees
            form_login:
                check_path: bengor_user_employee_security_login_check
                login_path: bengor_user_employee_security_login
                failure_path: bengor_user_employee_security_login
            logout:
                path: bengor_user_employee_security_logout
                target: /
    access_control:
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/login_check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/register$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/me, role: ROLE_USER }
        
        - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/login_check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/register$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin, role: ROLE_USER }
```
