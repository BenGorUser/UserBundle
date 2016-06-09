#FAQ

**I can't call methods in the entity such as login, logout, invite using user stored inside the session**

This is the expected behavior, the user in the session is read only. This is made to protect the domain against 
unexpected changes. In case you want to made a change to the user use a built in [command](commands.md).


**I get the following error: Unable to generate a URL for the named route "bengor_user.your_user_type.login"
as such route does not exist.**

Make sure you have added the following code to your `routing.yml` file

```yml
ben_gor_user:
    resource: '@BenGorUserBundle/Resources/config/routing/all.yml'
```

