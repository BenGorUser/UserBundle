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
        user: AppBundle\Entity\User
```
And for example if you execute the `php app/console debug:container | grep bengor.user.application.service.log_in`
you'll see the following:
```bash
bengor.user.application.service.log_in_user              BenGor\User\Application\Service\LogOutUserService
```
Otherwise, if your `user_class` contains multiple choices for example something like this
```yml
ben_gor_user:
    user_class:
        applicant: AppBundle\Entity\Applicant
        employee: AppBundle\Entity\Employee
```
the above command will print the following:
```bash
bengor.user.application.service.log_in_applicant         BenGor\User\Application\Service\LogOutUserService
bengor.user.application.service.log_in_employee          BenGor\User\Application\Service\LogOutUserService
```
