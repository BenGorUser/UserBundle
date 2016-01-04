#Configuration Reference

All available configuration options are listed below with their default values.
```yml
ben_gor_user:
    user_class:
        user:                                                               # Required at least one element, the name is not relevant
            class: ~                                                        # Required
            firewall:
                name: ~                                                     # Required
                pattern: ''
    subscribers:                                                            # By default is null
        invited_mailer:
            mail: ~                                                         # Required, can be swift_mailer or mandrill
            content: ~
            twig: @bengor_user/Email/invite.html.twig
        registered_mailer:
            mail: ~                                                         # Required, can be swift_mailer or mandrill
            content: ~
            twig: @bengor_user/Email/register.html.twig
        remember_password_requested:
            mail: ~                                                         # Required, can be swift_mailer or mandrill
            content: ~
            twig: @bengor_user/Email/remember_password_requested.html.twig
```
