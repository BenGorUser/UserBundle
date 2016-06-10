#Using existing use cases

To use one of the existing commands you just need to select the proper bus and select the command you
want to use. One bus is created for each [user type](usage_multiple_users.md) so select the correct one.

In the following examples `EnableUserCommand` is used to demonstrate the usage of the command bus. In case you want the
whole list check [command reference](https://github.com/BenGorUser/User/blob/master/docs/command.md) in the BenGor 
User library. The usage is the same, a command instance needs to be passed as first parameter of the `handle()` method
of the command bus.

In case you are triggering the command from a *Symfony controller* do the following replacing `$userType` by your 
[user type](usage_multiple_users.md).

```php 
$this->get('bengor_user.' . $userType . '.command_bus')->handle(
    new EnableUserCommand($confirmationToken)
);
```

In case you want to trigger the command from a *Symfony service* you need to 
[inject it in as usual](http://symfony.com/doc/current/book/service_container.html#referencing-injecting-services), 
replacing `your_user_type` by your user type.

```yml
# app/config/services.yml
services:
    app.your_service:
         class: AppBundle\Service\YourService
         arguments: ['@bengor_user.your_user_type.command_bus']
```
 
```php
// src/AppBundle/Service/YourService.php

use  BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;

class YourService 
{
    private $commandBus;
    
    public function __construct(UserCommandBus $commandBus) 
    {
        $this->commandBus = $commandBus;
    }
    
    public function doWhatever($confirmationToken) 
    {
       $this->commandBus->handle(
            new EnableUserCommand($confirmationToken)
       );
    }
}

- Back to the [index](index.md).
