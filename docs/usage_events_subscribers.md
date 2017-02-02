# Domain events and subscribers

The [BenGorUser's User library](https://github.com/BenGorUser/User) has some built-in subscribers that need to be added
 to make them work, the bundle does not register them by default.
 
The declaration mainly relies in the tag name `bengor_user_your_user_type_event_subscriber` and the subscribe to parameter.
In the tag name you will need to change `your_user_type` by your user type and in the subscribes to you will need to 
add a fully qualified name you want to listen to.

> A whole list of default events is available in the 
[BenGorUser's standalone library docs](https://github.com/BenGorUser/User/blob/master/docs/events.md)

User Invited Event Subscriber:
```yml
app.invite_url_generator:
    public: false
    class: BenGorUser\SymfonyRoutingBridge\Infrastructure\Routing\SymfonyUserUrlGenerator
    arguments:
        - "@router"
        - bengor_user_your_user_type_sign_up

app.invited_mailer_subscriber:
    class: BenGorUser\User\Domain\Event\UserInvitedMailerSubscriber
    arguments:
        - "@bengor_user.mailer.swift_mailer"
        - "@bengor_user.mailable_factory.invite"
        - "@app.invite_url_generator"
    tags:
        -
            name: bengor_user_your_user_type_event_subscriber
            subscribes_to: BenGorUser\User\Domain\Model\Event\UserInvited
        -
            name: bengor_user_your_user_type_event_subscriber
            subscribes_to: BenGorUser\User\Domain\Model\Event\UserInvitationTokenRegenerated
```
User Registered Event Subscriber:
```yml
app.sign_up_url_generator:
    public: false
    class: BenGorUser\SymfonyRoutingBridge\Infrastructure\Routing\SymfonyUserUrlGenerator
    arguments:
        - "@router"
        - bengor_user_your_user_type_enable

app.registered_mailer_subscriber:
    class: BenGorUser\User\Domain\Event\UserRegisteredMailerSubscriber
    arguments:
        - "@bengor_user.mailer.swift_mailer"
        - "@bengor_user.mailable_factory.sign_up"
        - "@app.sign_up_url_generator"
    tags:
        -
            name: bengor_user_your_user_type_event_subscriber
            subscribes_to: BenGorUser\User\Domain\Model\Event\UserRegistered
```
User Request Remember Password Event Subscriber:
```yml
app.request_remember_password_url_generator:
    public: false
    class: BenGorUser\SymfonyRoutingBridge\Infrastructure\Routing\SymfonyUserUrlGenerator
    arguments:
        - "@router"
        - bengor_user_your_user_type_change_password

app.request_remember_password_mailer_subscriber:
    class: BenGorUser\User\Domain\Event\UserRememberPasswordRequestedMailerSubscriber
    arguments:
        - "@bengor_user.mailer.swift_mailer"
        - "@bengor_user.mailable_factory.request_remember_password"
        - "@app.request_remember_password_url_generator"
    tags:
        -
            name: bengor_user_your_user_type_event_subscriber
            subscribes_to: BenGorUser\User\Domain\Model\Event\UserRememberPasswordRequested
```
> Remember that alternatively you can use the plain strategy of the user url generator avoiding the
> `SymfonyRoutingBridge` dependency. The following code is a declaration of this implementation:
```yml
app.invite_url_generator:
    public: false
    class: BenGorUser\User\Infrastructure\Routing\PlainUserUrlGenerator
    arguments:
        - "http://kreta.io?token={token}"
```

- Back to the [index](index.md).
