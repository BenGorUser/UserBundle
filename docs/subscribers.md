# Subscribers

TODO: Need explanation

```yml
app_user.invited_mailer_subscriber:
    class: BenGor\User\Domain\Event\UserInvitedMailerSubscriber
    arguments:
        - '@bengor_user.mailer.swift_mailer'
        - '@bengor_user.mailable_factory.invite'
        - '@bengor_user.symfony_url_generator'
        - bengor_user_user_sign_up
        - AppBundle\Entity\User
    tags:
        - { name: bengor_user_subscriber }

app_user.registered_mailer_subscriber:
    class: BenGor\User\Domain\Event\UserRegisteredMailerSubscriber
    arguments:
        - '@bengor_user.mailer.swift_mailer'
        - '@bengor_user.mailable_factory.sign_up'
        - '@bengor_user.symfony_url_generator'
        - bengor_user_user_enable
        - AppBundle\Entity\User
    tags:
        - { name: bengor_user_subscriber }

app_user.request_remember_password_mailer_subscriber:
    class: BenGor\User\Domain\Event\UserRememberPasswordRequestedMailerSubscriber
    arguments:
        - '@bengor_user.mailer.swift_mailer'
        - '@bengor_user.mailable_factory.invite'
        - '@bengor_user.symfony_url_generator'
        - bengor_user_user_change_password
        - AppBundle\Entity\User
    tags:
        - { name: bengor_user_subscriber }
```
