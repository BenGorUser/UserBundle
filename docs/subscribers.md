# Subscribers

TODO: Need explanation

```yml
app_user.invited_mailer_subscriber:
    class: BenGor\User\Domain\Event\UserInvitedMailerSubscriber
    arguments:
        - '@bengor.user.infrastructure.mailing.mailer.swift_mailer'
        - '@bengor.user.infrastructure.mailing.mailable.twig.invite_user_mailable_factory'
        - '@bengor.user.infrastructure.routing.symfony_url_generator'
        - bengor_user_user_sign_up
        - AppBundle\Entity\User
    tags:
        - { name: bengor_user_subscriber }

app_user.registered_mailer_subscriber:
    class: BenGor\User\Domain\Event\UserRegisteredMailerSubscriber
    arguments:
        - '@bengor.user.infrastructure.mailing.mailer.swift_mailer'
        - '@bengor.user.infrastructure.mailing.mailable.twig.sign_up_user_mailable_factory'
        - '@bengor.user.infrastructure.routing.symfony_url_generator'
        - bengor_user_user_enable
        - AppBundle\Entity\User
    tags:
        - { name: bengor_user_subscriber }

app_user.request_remember_password_mailer_subscriber:
    class: BenGor\User\Domain\Event\UserRememberPasswordRequestedMailerSubscriber
    arguments:
        - '@bengor.user.infrastructure.mailing.mailer.swift_mailer'
        - '@bengor.user.infrastructure.mailing.mailable.twig.invite_user_mailable_factory'
        - '@bengor.user.infrastructure.routing.symfony_url_generator'
        - bengor_user_user_change_password
        - AppBundle\Entity\User
    tags:
        - { name: bengor_user_subscriber }

```
