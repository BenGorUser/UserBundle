#Configuring routes used

This bundle generates all routes required to perform all the use cases on the fly. By default name and paths for each
use case have been defined as you can see in the following configuration file:

```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        your_user:
            # ...
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
                    success_redirection_route: bengor_user_user_homepage
            # ...
```

By default BenGorUserBundle generates the name and the path using the following rules:

* For name: bengor_user_ + *user_type_name* + *route_name*
* For path: / + *user_type_name* + / + *route_name*

In case you want to change some of the routes you need to override the configuration. For example, in case you want to 
change the login route for our *employee* user type you need to do the following:

```
# app/config/config.yml
ben_gor_user:
    user_class:
        employee:
            # ...
            routes:
                security:
                    login:
                        name: bengor_user_employee_login
                        path: /employee/new-login-route

