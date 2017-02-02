# Creating a custom command

We strongly recommended to implement your own command in case you need to implement a custom use case for your domain.

In this example we will define a use case where a User can subscribe to a newsletter. We will store a flag containing
if the User is subscribed in the domain and we will define a Command and a Handler to modify the domain in case a 
User wants to subscribe/unsubscribe to our newsletter.

## Modifying the domain

```php
// src/AppBundle/Entity/User.php

/**
 * @ORM\Entity
 * @ORM\Table(name="bengor_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="boolean")
     */
    private $isSubscribedToNewsletter;

    protected function __construct(
        UserId $anId,
        UserEmail $anEmail,
        UserPassword $aPassword = null,
        array $userRoles = []
    ) {
        parent::__construct($anId, $anEmail, $aPassword, $userRoles);
        $this->isSubscribedToNewsletter = false;
    }

    public function subscribeToNewsletter()
    {
        if ($this->isSubscribedToNewsletter) {
            throw new UserAlreadySubscribedToNewsletter();
        }

        $this->isSubscribedToNewsletter = true;
    }

    public function unsubscribeToNewsletter()
    {
        if (!$this->isSubscribedToNewsletter) {
            throw new UserNotSubscribedToNewsletter();
        }

        $this->isSubscribedToNewsletter = false;
    }
    
    public function isSubscribedToNewsletter()
    {
        return $this->isSubscribedToNewsletter;
    }
}
```

## Create the command

The command will define the data required to execute the action we need:

```php
// src/AppBundle/User/Application/Command/SubscribeToNewsletter/SubscribeToNewsletterCommand.php

class SubscribeToNewsletterCommand
{
    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function email()
    {
        return $this->email;
    }
}
```

## Create the handler

The handler will be the responsible to execute the commands. The bus calls `__invoke()` method so it needs to implement
the code responsible of subscribing the user to newsletter

```php
// src/AppBundle/User/Application/Command/SubscribeToNewsletter/SubscribeToNewsletterHandler.php

class SubscribeToNewsletterHandler
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * This must be __invoke() as the bus call this method.
     */
    public function __invoke($command)
    {
        $user = $this->repository->userOfEmail(new UserEmail($command->email()));
        if(null === $user) {
            throw new UserDoesNotExistException();
        }
        $user->subscribeToNewsletter();
        
        $this->repository->persist($user);
    }
}
```

## Register as a service

Once the command and the handler are implemented you need to create a service and tag it to let the command bus know 
you want to add a new handlers.

```php
# app/config/services.yml

app.user.command.subscribe_to_newsletter_handler:
    class: AppBundle\User\Command\SubscribeToNewsletterHandler
    arguments:
        - "@bengor_user.user.repository"
    tags:
        -
            name: bengor_user_your_user_type_command_bus_handler
            handles: AppBundle\User\Command\SubscribeToNewsletterCommand
```

> Make sure you add the correct user repository (depending the user type) and the correct command bus handler replacing
the string `your_user_type` in the tag's name. The `handles` parameter must have the fully qualified name of a command
you have created.

## Exposing the value in the DTO

*(Optional)* This bundle does not expose the domain when you use `$this->getUser()` in a controller for example. Instead,
it exposes a DTO. This is made to avoid modifying the aggregate root (User) from outside the domain.
  
To add `isSubscribedToNewsletter` as a DTO property two steps, extend the default DTO and add it to the config.

```php
// src/AppBundle/User/Application/DataTransformer/UserDTODataTransformer.php

class UserDTODataTransformer extends BaseUserDTODataTransformer 
{
    public function read()
    {
        return  array_merge(parent::read(), [
            'isSubscribedToNewsletter' => $this->user->isSubscribedToNewsletter(),
        ]) ;
    }
}
```

## Using it

Now you can use the use case you have just created, for example, in a controller:

```php
/**
 * @Route("/user/subscribe/", name="app_user_subscribe")
 */
public function subscribeToNewsletter(Request $request)
{
    $command = new SubscribeToNewsletterCommand($this->getUser()->getUsername());
    $this->get('bengor_user.your_user_type_command_bus')->handle($command);

    return new Response('Subscribed!');
}
```

> Make sure you use the command bus related to the user type you want to change

- Back to the [index](index.md).
