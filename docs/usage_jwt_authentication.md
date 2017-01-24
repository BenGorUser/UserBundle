#Using JWT authentication

Apart of the normal authentication via form login, this bundle comes with some predefined basics to use JWT
authentication in an easy way. For that purpose, the bundle needs the
[LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle). This chapters shows the
correct way to become BenGorUserBundle compatible with JWT authentication.

For more information about this bundle please, check its [documentation](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md).
 
Install the *LexikJWTAuthenticationBundle*:
```bash
$ composer require lexik/jwt-authentication-bundle
```
Register the bundle in `app/AppKernel.php`:
```php
public function registerBundles()
{
    return [
        // ...
        new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
    ];
}
```
Generate the SSH keys:
```bash
$ mkdir var/jwt
$ openssl genrsa -out var/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem
```
Configure the SSH keys path in your `config.yml`:
```yaml
# app/config/config.yml

lexik_jwt_authentication:
    private_key_path: "%kernel.root_dir%/../var/jwt/private.pem"
    public_key_path: "%kernel.root_dir%/../var/jwt/public.pem"
    pass_phrase: ""
    token_ttl: 3600
    user_identity_field: email
```
After that, updated the BenGorUserBundle's configuration itself:
```yml
# app/config/config.yml

ben_gor_user:
    user_class:
        user:
            class: AppBundle\Entity\User
            firewall: main
            use_cases:
                security:
                    api_enabled: true
            routes:
                security:
                    jwt:
                        name: bengor_user_user_jwt
                        path: /user/api/token
```
In order to make compatible with the new JWT authentication system you should update the `security.yml`:
```yml
# app/config/security.yml

security:
    encoders:
        AppBundle\Entity\User: bcrypt
    providers:
        bengor_user:
            id: bengor_user.user.provider
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        api:
            pattern: ^/user/api
            anonymous: true
            stateless: true
            guard:
                authenticators:
                    - lexik_jwt_authentication.jwt_token_authenticator
            provider: bengor_user
        main:
            anonymous: ~
            pattern: ^/user
            guard:
                authenticators:
                    - bengor_user.user.form_login_authenticator
            provider: bengor_user
            form_login:
                check_path: bengor_user_user_login_check
                login_path: bengor_user_user_login
                failure_path: bengor_user_user_login
            logout:
                path: bengor_user_user_logout
                target: /
    access_control:
        - { path: ^/user/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/sign-up, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/enable, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/api/token, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user/, role: ROLE_USER }
```

The built-in solution of JWT that provides BenGorUserBundle works with Basic Authorization. To
generate the token in the different clients you should check the following alternatives:

In the **Advance REST Client** you need to check the edit button and fill the modal form.

![Advance REST Client](https://rawgithub.com/BenGorUser/UserBundle/master/docs/_images/jwt_basic_authorization_arc.png)

In the **JavaScript** code with the `fetch` API:
```js
fetch('http://bengor-user-bundle-rocks.com/user/api/token', {
  headers: {
    method: 'POST',
    Authorization: `Basic ${btoa('bengor@user.com:123456')}`
  }
});
```

- Back to the [index](index.md).
