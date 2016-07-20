#Disabling login/logout

In case you don`t need user login/logout features, change the config to disable url generation for the given user class

```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        your_user:
            # ...
            use_cases:
                security:
                    enabled: false
            # ...
```
