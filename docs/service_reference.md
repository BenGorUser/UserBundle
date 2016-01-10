#Service Reference

All available services are listed below with their associated class.
```bash
bengor.user.application.service.activate_user_account                                              BenGor\User\Application\Service\ActivateUserAccountService
bengor.user.application.service.change_user_password                                               BenGor\User\Application\Service\ChangeUserPasswordService
bengor.user.application.service.change_user_password_using_remember_password_token                 BenGor\User\Application\Service\ChangeUserPasswordUsingRememberPasswordTokenService
bengor.user.application.service.invite_user                                                        BenGor\User\Application\Service\InviteUserService
bengor.user.application.service.log_in_user                                                        BenGor\User\Application\Service\LogInUserService
bengor.user.application.service.log_out_user                                                       BenGor\User\Application\Service\LogOutUserService
bengor.user.application.service.remove_user                                                        BenGor\User\Application\Service\RemoveUserService
bengor.user.application.service.request_user_remember_password_token                               BenGor\User\Application\Service\RequestRememberPasswordTokenService
bengor.user.application.service.sign_up_user                                                       BenGor\User\Application\Service\SignUpUserService
bengor.user.application.service.sign_up_user_by_invitation                                         BenGor\User\Application\Service\SignUpUserByInvitationService

bengor.user.infrastructure.domain.model.user_factory                                               BenGor\User\Infrastructure\Domain\Model\UserFactory

bengor.user.infrastructure.mailing.mailable.twig.invite_user_mailable_factory                      BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor.user.infrastructure.mailing.mailable.twig.register_user_mailable_factory                    BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory
bengor.user.infrastructure.mailing.mailable.twig.remember_password_request_user_mailable_factory   BenGor\User\Infrastructure\Mailing\Mailable\Twig\TwigUserMailableFactory

bengor.user.infrastructure.mailing.mailer.mandrill                                                 BenGor\User\Infrastructure\Mailing\Mandrill\MandrillUserMailer
bengor.user.infrastructure.mailing.mailer.swift_mailer                                             BenGor\User\Infrastructure\Mailing\SwiftMailer\SwiftMailerUserMailer

bengor.user.infrastructure.persistence.doctrine.user_guest_repository                              BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserGuestRepository
bengor.user.infrastructure.persistence.doctrine.user_repository                                    BenGor\User\Infrastructure\Persistence\Doctrine\DoctrineUserRepository
bengor.user.infrastructure.persistence.in_memory.user_guest_repository                             BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserGuestRepository
bengor.user.infrastructure.persistence.in_memory.user_repository                                   BenGor\User\Infrastructure\Persistence\InMemory\InMemoryUserRepository
bengor.user.infrastructure.persistence.sql.user_guest_repository                                   BenGor\User\Infrastructure\Persistence\Sql\SqlUserGuestRepository
bengor.user.infrastructure.persistence.sql.user_repository                                         BenGor\User\Infrastructure\Persistence\Sql\SqlUserRepository

bengor.user.infrastructure.security.symfony.user_password_encoder                                  BenGor\User\Infrastructure\Security\Symfony\SymfonyUserPasswordEncoder
bengor.user_bundle.security.form_login_user_authenticator                                          BenGor\UserBundle\Security\FormLoginAuthenticator

bengor.user_bundle.event_listener.domain_event_publisher                                           BenGor\UserBundle\EventListener\DomainEventPublisherListener





// Alias of Doctrine Transactional services

bengor_user.doctrine_user_guest_repository                                                         alias for "bengor.user.infrastructure.persistence.doctrine.user_guest_repository"                                      
bengor_user.doctrine_user_repository                                                               alias for "bengor.user.infrastructure.persistence.doctrine.user_repository"      

bengor_user.activate_user_account                                                                  alias for "bengor.user.application.service.activate_user_account_doctrine_transactional"                               
bengor_user.change_user_password                                                                   alias for "bengor.user.application.service.change_user_password_doctrine_transactional"                                
bengor_user.change_user_password_using_remember_password_token                                     alias for "bengor.user.application.service.change_user_password_using_remember_password_token_doctrine_transactional"  
bengor_user.invite_user                                                                            alias for "bengor.user.application.service.invite_user_doctrine_transactional"                                         
bengor_user.log_in_user                                                                            alias for "bengor.user.application.service.log_in_user_doctrine_transactional"                                         
bengor_user.log_out_user                                                                           alias for "bengor.user.application.service.log_out_user_doctrine_transactional"                                        
bengor_user.remove_user                                                                            alias for "bengor.user.application.service.remove_user_doctrine_transactional"                                         
bengor_user.request_user_remember_password_token                                                   alias for "bengor.user.application.service.request_user_remember_password_token_doctrine_transactional"                
bengor_user.sign_up_user                                                                           alias for "bengor.user.application.service.sign_up_user_doctrine_transactional"                                        
bengor_user.sign_up_user_by_invitation                                                             alias for "bengor.user.application.service.sign_up_user_by_invitation_doctrine_transactional"                          

bengor_user.user_factory                                                                           alias for "bengor.user.infrastructure.domain.model.user_factory"

bengor_user.symfony_user_password_encoder                                                          alias for "bengor.user.infrastructure.security.symfony.user_password_encoder"
bengor_user.form_login_user_authenticator                                                          alias for "bengor.user_bundle.security.form_login_user_authenticator_doctrine_transactional"
```
