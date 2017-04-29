# Configuration Reference

All available configuration options are listed below with their default values.
```yml
ben_gor_user:
    user_class:
        user:
            class: ~                           # Required
            firewall: ~                        # Required
            persistence: doctrine_orm          # Also, it can be "doctrine_odm_mongodb"
            data_transformer: BenGorUser\User\Application\DataTransformer\UserDTODataTransformer
            default_roles:
                - ROLE_USER
            use_cases:
                security:
                    enabled: true
                    api_enabled: false
                sign_up:
                    enabled: true
                    type: default              # Also, it can be "with_confirmation" or "by_invitation"
                    api_enabled: false
                    api_type: default          # Also, it can be "with_confirmation" or "by_invitation"
                change_password:
                    enabled: true
                    type: default              # Also, it can be "by_request_remember_password"
                    api_enabled: false
                    api_type: default          # Also, it can be "by_request_remember_password"
                remove:
                    enabled: true
                    api_enabled: false
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
                    success_redirection_route:
                        type: referer          # Also, it can be "force"
                        route: bengor_user_user_homepage
                    jwt:
                        name: bengor_user_user_jwt
                        path: /user/api/token
                sign_up:
                    name: bengor_user_user_sign_up
                    path: /user/sign-up
                    api_name: bengor_user_user_api_sign_up
                    api_path: /api/user/sign-up
                invite:
                    name: bengor_user_user_invite
                    path: /user/invite
                    success_redirection_route: ~
                    api_name: bengor_user_user_api_invite
                    api_path: /api/user/invite
                resend_invitation:
                    name: bengor_user_user_resend_invitation
                    path: /user/resend-invitation
                    success_redirection_route: ~
                    api_name: bengor_user_user_api_resend_invitation
                    api_path: /api/user/resend-invitation
                enable:
                    name: bengor_user_user_enable
                    path: /user/enable?confirmation-token={confirmation-token}
                    success_redirection_route: bengor_user_user_homepage
                    api_name: bengor_user_user_api_enable
                    api_path: /api/user/sign-up
                change_password:
                    name: bengor_user_user_change_password
                    path: /user/change-password
                    success_redirection_route: ~
                    api_name: bengor_user_user_api_change_password
                    api_path: /api/user/change-password
                request_remember_password:
                    name: bengor_user_user_request_remember_password
                    path: /user/remember-password
                    success_redirection_route: ~
                    api_name: bengor_user_user_api_remember_password
                    api_path: /api/user/remember-password
                remove:
                    name: bengor_user_user_remove
                    path: /user/remove
                    success_redirection_route: bengor_user_user_homepage
                    api_name: bengor_user_user_api_remove
                    api_path: /api/user/remove
```

- Back to the [index](index.md).
