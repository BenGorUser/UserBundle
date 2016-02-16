#Configuration Reference

All available configuration options are listed below with their default values.
```yml
ben_gor_user:
    user_class:
        user:                                  # Required at least one element, the name is not relevant
            class: ~                           # Required
            default_roles:
                - ROLE_USER
            firewall:
                name: ~                        # Required
                route_prefix_name: ''
                route_prefix_path: ''
                success_route_name: homepage
                success_route_path: /
            security:
                path: /login
                success_route_name: ~
                success_route_path: ~
            registration:
                type: default                  # Also, it can be 'none' or 'by_invitation'
                path: /register
                invite_path: /invite
                success_route_name: ~
                success_route_path: ~
```
