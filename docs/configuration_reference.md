#Configuration Reference

All available configuration options are listed below with their default values.
```yml
ben_gor_user:
    user_class:
        user:
            class: ~                           # Required
            firewall: ~                        # Required
            persistence: doctrine              # Also, it can be "sql"
            default_roles:
                - ROLE_USER
            routes:
                security:
                    enabled: true
                    login:
                        name: bengor_user_user_security_login
                        path: /user/login
                    login_check:
                        name: bengor_user_user_security_login_check
                        path: /user/login_check
                    logout:
                        name: bengor_user_user_security_logout
                        path: /user/logout
                    success_redirection_route: bengor_user_user_homepage
                registration:
                    enabled: true
                    type: default              # Also, it can be "by_invitation"
                    name: bengor_user_user_registration
                    path: /user/register
                    invitation_name: bengor_user_user_invitation
                    invitation_path: /user/invite
                    success_redirection_route: bengor_user_user_homepage
```
