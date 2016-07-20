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

* `default`: Will require old password and new password to perform user password change action
* `by_request_remember_password`: Will send a token to the user email enabling a form in which the user will change the 
password
