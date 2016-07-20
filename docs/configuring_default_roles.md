#Configuring default roles

You can define a list of default roles for new users. Just add the following in your user config file

```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        your_user:
            # ...
            default_roles:
                - ROLE_USER
                - ROLE_OTHER
                - ROLE_ANOTHER
            # ...
```
