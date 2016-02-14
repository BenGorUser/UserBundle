<?php

/*
 * This file is part of the BenGorUserBundle bundle.
 *
 * (c) Beñat Espiña <benatespina@gmail.com>
 * (c) Gorka Laucirica <gorka.lauzirika@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BenGor\UserBundle\Command;

use BenGor\User\Application\Service\SignUpUserRequest;
use BenGor\User\Domain\Model\User;
use BenGor\User\Domain\Model\UserRole;
use Ddd\Application\Service\TransactionalApplicationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Sign up user command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpUserCommand extends Command
{
    /**
     * Fully qualified class name.
     *
     * @var string
     */
    private $fqcn;

    /**
     * The sign up user service.
     *
     * @var TransactionalApplicationService
     */
    private $service;

    /**
     * The type of user class.
     *
     * @var string
     */
    private $userClass;

    /**
     * Constructor.
     *
     * @param TransactionalApplicationService $service   The sign up user service
     * @param string                          $userClass The user class
     * @param string                          $fqcn      The fully qualified class name
     */
    public function __construct(TransactionalApplicationService $service, $userClass, $fqcn)
    {
        parent::__construct();

        $this->fqcn = $fqcn;
        $this->service = $service;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('bengor:user:create')
            ->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument(
                    'email',
                    InputArgument::REQUIRED,
                    'The email'
                ),
                new InputArgument(
                    'password',
                    InputArgument::REQUIRED,
                    'The password'
                ),
                new InputArgument(
                    'roles',
                    InputArgument::IS_ARRAY,
                    'User roles (separate multiple roles with a space)'
                ),
            ])
            ->setHelp(<<<EOT
The <info>bengor:user:create</info> command creates a user:

  <info>php bin/console bengor:user:create benatespina@gmail.com</info>

This interactive shell will ask you for a password and then roles.

You can alternatively specify the password and roles as the second and third arguments:

  <info>php bin/console bengor:user:create benatespina@gmail.com 123456 ROLE_USER ROLE_ADMIN</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->service->execute(
            new SignUpUserRequest(
                $input->getArgument('email'),
                $input->getArgument('password'),
                $input->getArgument('roles')
            )
        );
        $user = $response->user();

        $output->writeln(
            sprintf(
                'Created user <comment>%s</comment>',
                $user->email()->email()
            )
        );
        $output->writeln(
            sprintf(
                'If you want to activate account this is the confirmation token <comment>%s</comment>',
                $user->confirmationToken()->token()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = [];
        if (!$input->getArgument('email')) {
            $question = new Question('Please choose an email:');
            $question->setValidator(function ($email) {
                if (empty($email)) {
                    throw new \Exception('Email can not be empty');
                }

                return $email;
            });
            $questions['email'] = $question;
        }
        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }
        if (!$input->getArgument('roles')) {
            $question = new Question(
                sprintf(
                    'Please choose roles (separate multiple roles with a space) <info>[%s]</info>:',
                    implode(' ', call_user_func([$this->fqcn, 'availableRoles']))
                )
            );
            $question->setValidator(function ($roles) {
                if (empty($roles)) {
                    throw new \Exception('Al least, one role is required');
                }
                $roles = explode(' ', $roles);

                return array_map(function ($role) {
                    if (false === call_user_func([$this->fqcn, 'isRoleAllowed'], new UserRole($role))) {
                        throw new \Exception('Please choose roles from above array');
                    }

                    return $role;
                }, $roles);
            });
            $questions['roles'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
