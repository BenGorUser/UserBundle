#UPGRADE

##v0.7.x to v0.8.x

* JwtAuthenticator does not exist anymore so, you need to update your security.yml with lexik jwt authenticator service 
id.
```yml
# app/config/security.yml

# ...

stateless: true
guard:
    authenticators:
        - lexik_jwt_authentication.jwt_token_authenticator

# ...
```
* By default LexikJwtAuthenticationBundle uses username as default property but BenGorUser does not provide this field
in its domain model so, you need to add the following line to this bundle configuration.
```yml
# app/config/config.yml

lexik_jwt_authentication:
# ...
    user_identity_field: email
```
* In case you need to resend an invitation token you must use `ResendInvitationUserCommand` instead `InviteUserCommand`.
* `success_redirection_route` under `routes/security` configuration now requires two parameters `type` and `route`.
To keep the behavior from `v0.6.x` you need to select `type:force` and `route: the_route_that_you_want_to_redirect_to`  
```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        user:
            routes:
                security:
                    success_redirection_route:
                        type: referer          # Also, it can be "force"
                        route: bengor_user_user_homepage


##v0.6.x to v0.7.x

* Reset password and invitation tokens expire, check 1 hour for remember password and 1 week for invitation token 
matches your requirements.
* In case you need to resend an invitation token you must use `ResendInvitationUserCommand` instead `InviteUserCommand`.
* `success_redirection_route` under `routes/security` configuration now requires two parameters `type` and `route`.
To keep the behavior from `v0.6.x` you need to select `type:force` and `route: the_route_that_you_want_to_redirect_to`  
```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        user:
            routes:
                security:
                    success_redirection_route:
                        type: referer          # Also, it can be "force"
                        route: bengor_user_user_homepage
