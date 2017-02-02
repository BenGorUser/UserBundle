#Using an existing sign up type

BenGorUser's standalone User library has some built-in sign up methods that can be changed in the `config.yml`

```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall: main
            use_cases:
                sign_up:
                    type: default  # Also, it can be "with_confirmation", "by_invitation"
```

##Default
It registers the user and directly logs the user in. No need to add anything in the config.yml

##With confirmation
Allows the user to sign up but it needs to validate his email using the confirmation token that will be sent. 

You need to set type to `with_confirmation` and add the following subscriber replacing `your_user_type` by the user implementing
this sign up method in the last argument of the service and in the tag name.
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

##By invitation
Allows an existing user to send an invitation to a given email with a confirmation token that will allow the new user
to set his password and login into the application. 

You need to set type to `by_invitation` and add the following subscriber replacing `your_user_type` by the user implementing
this sign up method in the last argument of the service and in the tag name.

```yml
# app/config/services.yml

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
