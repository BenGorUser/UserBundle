# Commands

This bundle allows the use of all commands available in the BenGorUser component. You can find a list [here](https://github.com/BenGorUser/User/blob/master/docs/command.md)

To run one of these commands you need to use the command bus responsible of assigning the correct handler. To do it so,
you need to get the correct bus for the user type you want to modify as each one as its own bus. For example, to execute 
the log in command you will need to do the folowing, replacing `your_user_type_name` by you user type name:

```php
    $command = new \BenGorUser\User\Application\Command\LogIn\LogInUserCommand($email, $password);
    $this->get('bengor_user.your_user_type_name_command_bus')->handle($command);
```

## Creating a custom command

We strongly recommended to implement your own command in case you need to implement a custom use case for your domain.
To do it so, you will need to classes, the command and the handler.

In this example we will define a use case where a User can subscribe to a newsletter. This is stored in a flag inside
the User you have created following the steps in [basic configuration tutorial](basic_configuration.md):

```php
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
}
```

The command will define the data required to execute the action we need:

```php
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

The handler will be the responsible to execute the commands. The bus calls `__invoke()` method so it needs to implement
the code responsible of subscribing the user to newsletter

```php
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

Once the command and the subscribers are implemented you need to create a service and tag it to let the command bus know 
you want to add a new handlers. To do it add the following to your `services.yml`

```php

app.user.command.subscribe_to_newsletter_handler:
    class: AppBundle\User\Command\SubscribeToNewsletterHandler
    arguments:
        - '@bengor_user.user_repository'
    tags:
        - { name: bengor_user_your_user_type_command_bus_handler, handles: AppBundle\User\Command\SubscribeToNewsletterCommand }
```

> Make sure you add the correct user repository (depending the user type) and the correct command bus handler replacing
the string `your_user_type` in the tag's name.

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



