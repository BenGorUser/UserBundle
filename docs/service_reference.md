#Service Reference

All available services are listed below with their associated class.
```bash
#### Application Data Transformers ####
bengor.user.application.data_transformer.user_dto                           BenGorUser\User\Application\DataTransformer\UserDTODataTransformer
bengor.user.application.data_transformer.user_no_transformation             BenGorUser\User\Application\DataTransformer\UserNoTransformationDataTransformer

#### Application Services ####
bengor.user.application.service.change_user_password                        BenGorUser\User\Application\Service\ChangePassword\ChangeUserPasswordService
bengor.user.application.service.enable_user                                 BenGorUser\User\Application\Service\Enable\EnableUserService
bengor.user.application.service.invite_user                                 BenGorUser\User\Application\Service\Invite\InviteUserService
bengor.user.application.service.log_in_user                                 BenGorUser\User\Application\Service\LogIn\LogInUserService
bengor.user.application.service.log_out_user                                BenGorUser\User\Application\Service\LogOut\LogOutUserService
bengor.user.application.service.remove_user                                 BenGorUser\User\Application\Service\Remove\RemoveUserService
bengor.user.application.service.request_user_remember_password_token        BenGorUser\User\Application\Service\RequestRememberPassword\RequestRememberPasswordService
bengor.user.application.service.sign_up_user                                BenGorUser\User\Application\Service\SignUp\SignUpUserService
bengor.user.application.service.sign_up_user_default                        BenGorUser\User\Application\Service\SignUp\SignUpUserService

#### Aliases of Transactionl Application Services ####
bengor_user.change_user_password                                            Ddd\Application\Service\TransactionalApplicationService
bengor_user.enable_user                                                     Ddd\Application\Service\TransactionalApplicationService
bengor_user.invite_user                                                     Ddd\Application\Service\TransactionalApplicationService
bengor_user.log_in_user                                                     Ddd\Application\Service\TransactionalApplicationService
bengor_user.log_out_user                                                    Ddd\Application\Service\TransactionalApplicationService
bengor_user.remove_user                                                     Ddd\Application\Service\TransactionalApplicationService
bengor_user.request_user_remember_password_token                            Ddd\Application\Service\TransactionalApplicationService
bengor_user.sign_up_user                                                    Ddd\Application\Service\TransactionalApplicationService
bengor_user.sign_up_user_default                                            Ddd\Application\Service\TransactionalApplicationService

#### Other #####
bengor_user.mailable_factory_invite                                         BenGorUser\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor_user.mailable_factory_sign_up                                        BenGorUser\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor_user.mailable_factory_request_remember_password                      BenGorUser\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory

bengor_user.mailer.mandrill                                                 BenGorUser\User\Infrastructure\Mailing\Mailer\Mandrill\MandrillUserMailer
bengor_user.mailer.swift_mailer                                             BenGorUser\User\Infrastructure\Mailing\Mailer\SwiftMailer\SwiftMailerUserMailer

bengor_user.symfony_url_generator                                           BenGorUser\User\Infrastructure\Routing\Symfony\SymfonyUserUrlGenerator

bengor.user.command.create_user_command                                     BenGorUser\UserBundle\Command\CreateUserCommand                   

bengor.user.event_listener.domain_event_publisher                           BenGorUser\UserBundle\EventListener\DomainEventPublisherListener

bengor_user.form_login_user_authenticator                                   BenGorUser\UserBundle\Security\FormLoginAuthenticator

bengor_user.symfony_user_password_encoder                                   BenGorUser\User\Infrastructure\Security\Symfony\SymfonyUserPasswordEncoder

bengor_user.user_factory                                                    BenGorUser\User\Infrastructure\Domain\Model\UserFactory

bengor_user.user_repository                                                 BenGorUser\User\Infrastructure\Persistence\Doctrine\ORM\DoctrineORMUserRepository
                                                                            BenGorUser\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\DoctrineODMMongoDBUserRepository
                                                                            BenGorUser\User\Infrastructure\Persistence\Sql\SqlUserRepository
```
