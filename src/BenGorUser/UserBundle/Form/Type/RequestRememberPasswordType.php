<?php

/*
 * This file is part of the BenGorUser package.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGorUser\UserBundle\Form\Type;

use BenGorUser\User\Application\Command\RequestRememberPassword\RequestRememberPasswordCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Request remember password user form type.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class RequestRememberPasswordType extends AbstractType
{
    /**
     * The current logged user.
     *
     * @var UserInterface|null
     */
    private $currentUser;

    /**
     * Constructor.
     *
     * @param TokenStorageInterface $aTokenStorage The token storage
     */
    public function __construct(TokenStorageInterface $aTokenStorage)
    {
        $token = $aTokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $this->currentUser = $token->getUser() instanceof UserInterface
                ? $token->getUser()
                : null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RequestRememberPasswordCommand::class,
            'empty_data' => function (FormInterface $form) {
                $email = null === $this->currentUser
                    ? $form->get('email')->getData()
                    : $this->currentUser->getUsername();

                return new RequestRememberPasswordCommand($email);
            },
        ]);
    }
}
