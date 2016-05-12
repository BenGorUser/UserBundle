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

namespace BenGorUser\UserBundle\Command;

use BenGorUser\User\Application\Service\SignUp\SignUpUserCommand;
use BenGorUser\UserBundle\Application\Service\UserCommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Create user command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class CreateUserCommand extends Command
{
    /**
     * Fully qualified class name.
     *
     * @var string
     */
    private $fqcn;

    /**
     * The command bus.
     *
     * @var UserCommandBus
     */
    private $commandBus;

    /**
     * The type of user class.
     *
     * @var string
     */
    private $userClass;

    /**
     * Constructor.
     *
     * @param UserCommandBus $commandBus The command bus
     * @param string         $userClass  The user class
     * @param string         $fqcn       The fully qualified class name
     */
    public function __construct(UserCommandBus $commandBus, $userClass, $fqcn)
    {
        $this->fqcn = $fqcn;
        $this->commandBus = $commandBus;
        $this->userClass = $userClass;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(sprintf('bengor:user:%s:create', $this->userClass))
            ->setDescription(sprintf('Create a %s.', $this->userClass))
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
The <info>bengor:user:$this->userClass:create</info> command creates a $this->userClass:

  <info>php bin/console bengor:user:$this->userClass:create benatespina@gmail.com</info>

This interactive shell will ask you for a password and then roles.

You can alternatively specify the password and roles as the second and third arguments:

  <info>php bin/console bengor:user:$this->userClass:create benatespina@gmail.com 123456 ROLE_USER ROLE_ADMIN</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $response = $this->commandBus->handle(
            SignUpUserCommand::fromEmail(
                $input->getArgument('email'),
                $input->getArgument('password'),
                $input->getArgument('roles')
            )
        );

        $output->writeln(sprintf(
            'Created %s: <comment>%s</comment>', $this->userClass, $response['email']
        ));
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
                    $roles = implode(' ', call_user_func([$this->fqcn, 'availableRoles']));
                }

                return explode(' ', $roles);
            });
            $questions['roles'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
