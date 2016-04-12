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
                sign_up:
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
                        name: bengor_user_user_login
                        path: /user/login
                    login_check:
                        name: bengor_user_user_login_check
                        path: /user/login-check
                    logout:
                        name: bengor_user_user_logout
                        path: /user/logout
                    success_redirection_route: bengor_user_user_homepage
                sign_up:
                    name: bengor_user_user_sign_up
                    path: /user/sign-up
                invite:
                    name: bengor_user_user_invite
                    path: /user/invite
                    success_redirection_route: ~
                enable:
                    name: bengor_user_user_enable
                    path: /user/enable?confirmation-token={confirmation-token}
                    success_redirection_route: bengor_user_user_homepage
                change_password:
                    name: bengor_user_user_change_password
                    path: /user/change-password
                    success_redirection_route: ~
                request_remember_password:
                    name: bengor_user_user_request_remember_password
                    path: /user/remember-password
                    success_redirection_route: ~
                remove:
                    name: bengor_user_user_remove
                    path: /user/remove
                    success_redirection_route: ~
```
