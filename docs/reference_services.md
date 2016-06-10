#Service Reference

All available services are listed below with their associated class.
```bash
bengor_user.user.dto_data_transformer                    BenGorUser\User\Application\DataTransformer\UserDTODataTransformer
bengor_user.user.symfony_data_transformer                BenGorUser\UserBundle\Security\UserSymfonyDataTransformer

bengor_user.user.change_password                         alias for "bengor.user.application.command.change_user_password"
bengor_user.user.change_password_without_old_password    alias for "bengor.user.application.command.change_user_password_without_old_password"
bengor_user.user.user.request_remember_password          alias for "bengor.user.application.command.request_user_remember_password"
bengor_user.user.log_in                                  alias for "bengor.user.application.command.log_in_user"
bengor_user.user.enable                                  alias for "bengor.user.application.command.enable_user"
bengor_user.user.log_out                                 alias for "bengor.user.application.command.log_out_user"
bengor_user.user.remove                                  alias for "bengor.user.application.command.remove_user"
bengor_user.user.invite                                  alias for "bengor.user.application.command.invite_user"
bengor_user.user.sign_up                                 alias for "bengor.user.application.command.sign_up_user"
bengor_user.user.sign_up_default                         alias for "bengor.user.application.command.sign_up_user_default"

bengor_user.user.command_bus                             BenGorUser\SimpleBusBridgeBundle\CommandBus\SimpleBusUserCommandBus
bengor_user.user.event_bus                               BenGorUser\SimpleBusBridgeBundle\EventBus\SimpleBusUserEventBus

bengor_user.user.factory_invite                          BenGorUser\User\Infrastructure\Domain\Model\UserFactoryInvite
bengor_user.user.factory_sign_up                         BenGorUser\User\Infrastructure\Domain\Model\UserFactorySignUp

bengor_user.user.by_of_email_query                       BenGorUser\User\Application\Query\UserOfEmailHandler
bengor_user.user.by_invitation_token_query               BenGorUser\User\Application\Query\UserOfInvitationTokenHandler
bengor_user.user.by_remember_password_token_query        BenGorUser\User\Application\Query\UserOfRememberPasswordTokenHandler

bengor_user.user.form_login_authenticator                BenGorUser\UserBundle\Security\FormLoginAuthenticator
bengor_user.user.provider                                BenGorUser\UserBundle\Security\UserProvider

bengor_user.user.symfony_password_encoder                BenGorUser\SymfonySecurityBridge\Infrastructure\Security\SymfonyUserPasswordEncoder

bengor_user.user.repository                              BenGorUser\DoctrineORMBridge\Infrastructure\Persistence\DoctrineORMUserRepository
                                                         BenGorUser\DoctrineODMBridge\Infrastructure\Persistence\DoctrineODMUserRepository
                                                         BenGorUser\User\Infrastructure\Persistence\Sql\SqlUserRepository

bengor_user.mailable_factory_invite                      BenGorUser\TwigBridge\Infrastructure\Mailing\TwigUserMailableFactory
bengor_user.mailable_factory_request_remember_password   BenGorUser\TwigBridge\Infrastructure\Mailing\TwigUserMailableFactory
bengor_user.mailable_factory_sign_up                     BenGorUser\TwigBridge\Infrastructure\Mailing\TwigUserMailableFactory

bengor_user.mailer_mandrill                              BenGorUser\User\Infrastructure\Mailing\Mailer\Mandrill\MandrillUserMailer
bengor_user.mailer_swift_mailer                          BenGorUser\SwiftMailerBridge\Infrastructure\Mailing\SwiftMailerUserMailer

bengor_user.symfony_url_generator                        BenGorUser\SymfonyRoutingBridge\Infrastructure\Routing\SymfonyUserUrlGenerator



// Creates by Symfony DIC to the correct use of the bundle. Please, don't use them.

bengor.user.application.command.change_user_password                         BenGorUser\User\Application\Command\ChangePassword\ChangeUserPasswordHandler
bengor.user.application.command.change_user_password_without_old_password    BenGorUser\User\Application\Command\ChangePassword\WithoutOldPasswordChangeUserPasswordHandler
bengor.user.application.command.request_user_remember_password_token         BenGorUser\User\Application\Command\RequestRememberPassword\RequestRememberPasswordHandler
bengor.user.application.command.log_in_user                                  BenGorUser\User\Application\Command\LogIn\LogInUserHandler
bengor.user.application.command.enable_user                                  BenGorUser\User\Application\Command\Enable\EnableUserHandler
bengor.user.application.command.log_out_user                                 BenGorUser\User\Application\Command\LogOut\LogOutUserHandler
bengor.user.application.command.remove_user                                  BenGorUser\User\Application\Command\Remove\RemoveUserHandler
bengor.user.application.command.invite_user                                  BenGorUser\User\Application\Command\SignUp\InviteUserHandler
bengor.user.application.command.sign_up_user                                 BenGorUser\User\Application\Command\SignUp\SignUpUserHandler
bengor.user.application.command.sign_up_user_default                         BenGorUser\User\Application\Command\SignUp\SignUpUserHandler

bengor.user.command.change_user_password_command                             BenGorUser\UserBundle\Command\ChangePasswordCommand
bengor.user.command.create_user_command                                      BenGorUser\UserBundle\Command\CreateUserCommand

bengor.user.form.type.change_password                                        BenGorUser\UserBundle\Form\Type\ChangePasswordType
bengor.user.form.type.change_password_by_request_remember_password           BenGorUser\UserBundle\Form\Type\ChangePasswordByRequestRememberPasswordType
bengor.user.form.type.invite                                                 BenGorUser\UserBundle\Form\Type\InviteType
bengor.user.form.type.remove                                                 BenGorUser\UserBundle\Form\Type\RemoveType
bengor.user.form.type.request_remember_password                              BenGorUser\UserBundle\Form\Type\RequestRememberPasswordType
bengor.user.form.type.sign_up                                                BenGorUser\UserBundle\Form\Type\SignUpType
bengor.user.form.type.sign_up_by_invitation                                  BenGorUser\UserBundle\Form\Type\SignUpByInvitationType
bengor.user.form.type.sign_up_by_invitation_with_confirmation                BenGorUser\UserBundle\Form\Type\SignUpByInvitationWithConfirmationType
bengor.user.form.type.sign_up_with_confirmation                              BenGorUser\UserBundle\Form\Type\SignUpWithConfirmationType
```

- Back to the [index](index.md).
