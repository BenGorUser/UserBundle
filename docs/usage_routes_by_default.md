#Using default routes

This bundle creates a group of routes for each declared user type.
> The following list shows the ones that are created by default for "user" user_type.

```
bengor_user_user_change_password             GET|POST    /user/change-password
bengor_user_user_enable                      GET         /user/enable
bengor_user_user_invite                      GET|POST    /user/invite
bengor_user_user_login                       GET|POST    /user/login
bengor_user_user_login_check                 POST        /user/login-check
bengor_user_user_logout                      GET         /user/logout
bengor_user_user_sign_up                     GET|POST    /user/sign-up
bengor_user_user_remove                      GET|POST    /user/remove
bengor_user_user_request_remember_password   GET|POST    /user/remember-password
bengor_user_user_resend_invitation           GET|POST    /user/resend-invitation
bengor_user_user_new_token                   POST        /user/api/token

```

Remember that you will need to add to your user types home page action with the following name:
`bengor_user_your_user_type_homepage`. This name is generated automatically for `success_redirection_route` in case
you want to change the name check [extending custom urls docs](extending_customize_urls.md)

All these routes can be changed in bundle's configuration.

- Back to the [index](index.md).
