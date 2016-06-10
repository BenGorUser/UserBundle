#Using console commands

Right now two Symfony commands are available and both are [user type](usage_multiple_users.md) aware. 

```
bengor:user:your_user_type:change-password        Change user's password.
bengor:user:your_user_type:create                 Create a user.
```

Run them as you would usually do with any Symfony command in the project root:

In Symfony 3:
```
bin/console bengor:user:your_user_type:create
```

In Symfony 2:
```
app/console bengor:user:your_user_type:create
```

For further info run the command with the `--help` flag:

```
bin/console bengor:user:your_user_type:create --help
```
