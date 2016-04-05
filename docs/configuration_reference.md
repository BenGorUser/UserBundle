#Configuration Reference

All available configuration options are listed below with their default values.
```yml
ben_gor_user:
    user_class:
        user:
            class: ~                           # Required
            firewall: ~                        # Required
            persistence: doctrine_orm          # Also, it can be "doctrine_odm_mongodb" or "sql"
            default_roles:
                - ROLE_USER
            use_cases:
                security:
                    enabled: true
                registration:
                    enabled: true
                    type: default              # Also, it can be "with_confirmation", "by_invitation" or "by_invitation_with_confirmation"
                change_password:
                    enabled: true
                    type: default              # Also, it can be "by_request_remember_password"
                remove:
                    enabled: true
            routes:
                security:
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
                    name: bengor_user_user_registration
                    path: /user/register
                    success_redirection_route: bengor_user_user_homepage
                    invitation:
                        name: bengor_user_user_invitation
                        path: /user/invite
                    user_enable:
                        name: bengor_user_user_enable
                        path: /user/confirmation-token
```
