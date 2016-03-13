#Service Reference

All available services are listed below with their associated class.
```bash
bengor.user.application.service.activate_user_account                                    BenGor\User\Application\Service\ActivateUserAccountService
bengor.user.application.service.change_user_password                                     BenGor\User\Application\Service\ChangeUserPasswordService
bengor.user.application.service.change_user_password_using_remember_password_token       BenGor\User\Application\Service\ChangeUserPasswordUsingRememberPasswordTokenService
bengor.user.application.service.invite_user                                              BenGor\User\Application\Service\InviteUserService
bengor.user.application.service.log_in_user                                              BenGor\User\Application\Service\LogInUserService
bengor.user.application.service.log_out_user                                             BenGor\User\Application\Service\LogOutUserService
bengor.user.application.service.remove_user                                              BenGor\User\Application\Service\RemoveUserService
bengor.user.application.service.request_user_remember_password_token                     BenGor\User\Application\Service\RequestRememberPasswordTokenService
bengor.user.application.service.sign_up_user                                             BenGor\User\Application\Service\SignUpUserService
bengor.user.application.service.sign_up_user_by_invitation                               BenGor\User\Application\Service\SignUpUserByInvitationService

bengor_user.user_factory                                                                 BenGor\User\Infrastructure\Domain\Model\UserFactory

bengor_user.doctrine_user_repository                                                     BenGor\File\Infrastructure\Persistence\Doctrine\DoctrineUserRepository
bengor_user.doctrine_user_guest_repository                                               BenGor\File\Infrastructure\Persistence\Doctrine\DoctrineUserGuestRepository
            ------------------------------------------------------------------------------------------------------------------------------
bengor_user.sql_user_repository                                                          BenGor\File\Infrastructure\Persistence\Doctrine\SqlUserRepository
bengor_user.sql_user_guest_repository                                                    BenGor\File\Infrastructure\Persistence\Doctrine\SqlUserGuestRepository

bengor_user.mailing.twig.invite_user_mailable_factory                                    BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor_user.mailing.twig.register_user_mailable_factory                                  BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor_user.mailing.twig.remember_password_request_user_mailable_factory                 BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory

bengor_user.mailing.mandrill_mailer                                                      BenGor\User\Infrastructure\Mailing\Mandrill\MandrillUserMailer
bengor_user.mailing.swift_mailer_mailer                                                  BenGor\User\Infrastructure\Mailing\SwiftMailer\SwiftMailerUserMailer

bengor.user.infrastructure.security.symfony.user_password_encoder                        BenGor\User\Infrastructure\Security\Symfony\SymfonyUserPasswordEncoder
bengor.user_bundle.security.form_login_user_authenticator                                BenGor\UserBundle\Security\FormLoginAuthenticator

bengor.user_bundle.event_listener.domain_event_publisher                                 BenGor\UserBundle\EventListener\DomainEventPublisherListener





// Aliases of transactional services

bengor_user.activate_user_account                                                        Ddd\Application\Service\TransactionalApplicationService
bengor_user.change_user_password                                                         Ddd\Application\Service\TransactionalApplicationService
bengor_user.change_user_password_using_remember_password_token                           Ddd\Application\Service\TransactionalApplicationService
bengor_user.invite_user                                                                  Ddd\Application\Service\TransactionalApplicationService
bengor_user.log_in_user                                                                  Ddd\Application\Service\TransactionalApplicationService
bengor_user.log_out_user                                                                 Ddd\Application\Service\TransactionalApplicationService
bengor_user.remove_user                                                                  Ddd\Application\Service\TransactionalApplicationService
bengor_user.request_user_remember_password_token                                         Ddd\Application\Service\TransactionalApplicationService
bengor_user.sign_up_user                                                                 Ddd\Application\Service\TransactionalApplicationService
bengor_user.sign_up_user_by_invitation                                                   Ddd\Application\Service\TransactionalApplicationService
```
