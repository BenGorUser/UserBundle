#UPGRADE

##v0.6.x to v0.7.x

* Reset password and invitation tokens expire, check 1 hour for remember password and 1 week for invitation token 
matches your requirements.
* In case you need to resend an invitation token you must use `ResendInvitationUserCommand` instead `InviteUserCommand`.
* `success_redirection_route` under `routes/security` configuration now requires two parameters `type` and `route`.
To keep the behavior from `v0.6.x` you need to select `type:force` and `route: the_route_that_you_want_to_redirect_to`  
```yml
ben_gor_user:
    user_class:
        user:
            routes:
                security:
                    success_redirection_route:
                        type: referer          # Also, it can be "force"
                        route: bengor_user_user_homepage
