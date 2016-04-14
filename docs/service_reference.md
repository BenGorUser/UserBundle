#Service Reference

All available services are listed below with their associated class.
```bash
#### Application Data Transformers ####
bengor.user.application.data_transformer.user_dto                           BenGor\User\Application\DataTransformer\UserDTODataTransformer
bengor.user.application.data_transformer.user_no_transformation             BenGor\User\Application\DataTransformer\UserNoTransformationDataTransformer

#### Application Services ####
bengor.user.application.service.change_user_password                        BenGor\User\Application\Service\ChangePassword\ChangeUserPasswordService
bengor.user.application.service.enable_user                                 BenGor\User\Application\Service\Enable\EnableUserService
bengor.user.application.service.invite_user                                 BenGor\User\Application\Service\Invite\InviteUserService
bengor.user.application.service.log_in_user                                 BenGor\User\Application\Service\LogIn\LogInUserService
bengor.user.application.service.log_out_user                                BenGor\User\Application\Service\LogOut\LogOutUserService
bengor.user.application.service.remove_user                                 BenGor\User\Application\Service\Remove\RemoveUserService
bengor.user.application.service.request_user_remember_password_token        BenGor\User\Application\Service\RequestRememberPassword\RequestRememberPasswordService
bengor.user.application.service.sign_up_user                                BenGor\User\Application\Service\SignUp\SignUpUserService
bengor.user.application.service.sign_up_user_default                        BenGor\User\Application\Service\SignUp\SignUpUserService

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
bengor.user.mailable_factory_invite                                         BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor.user.mailable_factory_sign_up                                        BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor.user.mailable_factory_request_remember_password                      BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory

bengor.user.mailer.mandrill                                                 BenGor\User\Infrastructure\Mailing\Mailer\Mandrill\MandrillUserMailer
bengor.user.mailer.swift_mailer                                             BenGor\User\Infrastructure\Mailing\Mailer\SwiftMailer\SwiftMailerUserMailer

bengor.user.infrastructure.routing.symfony_url_generator                    BenGor\User\Infrastructure\Routing\Symfony\SymfonyUserUrlGenerator

bengor.user.command.create_user_command                                     BenGor\UserBundle\Command\CreateUserCommand                   

bengor.user.event_listener.domain_event_publisher                           BenGor\UserBundle\EventListener\DomainEventPublisherListener

bengor_user.form_login_user_authenticator                                   BenGor\UserBundle\Security\FormLoginAuthenticator

bengor_user.symfony_user_password_encoder                                   BenGor\User\Infrastructure\Security\Symfony\SymfonyUserPasswordEncoder

bengor_user.user_factory                                                    BenGor\User\Infrastructure\Domain\Model\UserFactory

bengor_user.user_repository                                                 BenGor\User\Infrastructure\Persistence\Doctrine\ORM\DoctrineORMUserRepository
                                                                            BenGor\User\Infrastructure\Persistence\Doctrine\ODM\MongoDB\DoctrineODMMongoDBUserRepository
                                                                            BenGor\User\Infrastructure\Persistence\Sql\SqlUserRepository
```
