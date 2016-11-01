# Domain events and subscribers

The [BenGorUser's User library](https://github.com/BenGorUser/User) has some built-in subscribers that need to be added
 to make them work, the bundle does not register them by default.
 
The declaration mainly relies in the tag name `bengor_user_your_user_type_event_subscriber` and the subscribe to parameter.
In the tag name you will need to change `your_user_type` by your user type and in the subscribes to you will need to 
add a fully qualified name you want to listen to.

> A whole list of default events is available in the 
[BenGorUser's standalone library docs](https://github.com/BenGorUser/User/blob/master/docs/events.md)

```yml
app.invited_mailer_subscriber:
    class: BenGorUser\User\Domain\Event\UserInvitedMailerSubscriber
    arguments:
        - '@bengor_user.mailer.swift_mailer'
        - '@bengor_user.mailable_factory.invite'
        - '@bengor_user.symfony_url_generator'
        - bengor_user_your_user_type_sign_up
    tags:
        - { name: bengor_user_your_user_type_event_subscriber, subscribes_to: BenGorUser\User\Domain\Model\Event\UserInvited }
        - { name: bengor_user_your_user_type_event_subscriber, subscribes_to: BenGorUser\User\Domain\Model\Event\UserInvitationTokenRegenerated }

app.registered_mailer_subscriber:
    class: BenGorUser\User\Domain\Event\UserRegisteredMailerSubscriber
    arguments:
        - '@bengor_user.mailer.swift_mailer'
        - '@bengor_user.mailable_factory.sign_up'
        - '@bengor_user.symfony_url_generator'
        - bengor_user_your_user_type_enable
    tags:
        - { name: bengor_user_your_user_type_event_subscriber, subscribes_to: BenGorUser\User\Domain\Model\Event\UserRegistered }

app.request_remember_password_mailer_subscriber:
    class: BenGorUser\User\Domain\Event\UserRememberPasswordRequestedMailerSubscriber
    arguments:
        - '@bengor_user.mailer.swift_mailer'
        - '@bengor_user.mailable_factory.request_remember_password'
        - '@bengor_user.symfony_url_generator'
        - bengor_user_your_user_type_change_password
    tags:
        - { name: bengor_user_your_user_type_event_subscriber, subscribes_to: BenGorUser\User\Domain\Model\Event\UserRememberPasswordRequested }
```

- Back to the [index](index.md).
