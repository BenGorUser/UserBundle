#Configuring password change strategy

To select the desired type of password change strategy, you need to modify your user's config as follows:

```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        your_user:
            # ...
            use_cases:
                change_password:
                    type: <your-desired-type>
            # ...
```

You need to replace `<your-desired-type>` by one of the following types:

* `default`: User will be directly allowed to access once the sign up is completed
* `with_confirmation`: Will send a link to the user email to confirm a valid emails has been given. Once the link is 
 clicked, the user will be enabled.
* `by_invitation`: Will send and invitation email with a link to a form that allows to define a password to the given 
user email. Once the form is filled the user will be enabled 
