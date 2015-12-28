#Configuration Reference

All available configuration options are listed below with their default values.
```yml
ben_gor_user:
    user_class:
        user:                # Required at least one element, the name is not relevant
            class: ~         # Required
            firewall:
                name: ~      # Required
                pattern: ''
    subscribers: ~           # there are 3 implemented subscribers: "invited_mailer", "registered_mailer" and
                             # "remember_password_requested": their values can be "swift_mailer" or "mandrill"
```
