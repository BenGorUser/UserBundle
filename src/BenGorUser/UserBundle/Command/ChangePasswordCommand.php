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

use BenGorUser\User\Application\Service\ChangePassword\WithoutOldPasswordChangeUserPasswordCommand;
use BenGorUser\UserBundle\Application\UserCommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Change user password command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ChangePasswordCommand extends Command
{
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
     */
    public function __construct(UserCommandBus $commandBus, $userClass)
    {
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
            ->setName(sprintf('bengor:user:%s:change-password', $this->userClass))
            ->setDescription(sprintf('Change the password of a %s.', $this->userClass))
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
            ])
            ->setHelp(<<<EOT
The <info>bengor:user:$this->userClass:change-password</info> command changes password of a $this->userClass:

  <info>php bin/console bengor:user:$this->userClass:change-password benatespina@gmail.com</info>

This interactive shell will ask you for a new password.

You can alternatively specify the password after email, in inline:

  <info>php bin/console bengor:user:$this->userClass:change-password benatespina@gmail.com 123456</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandBus->handle(
            new WithoutOldPasswordChangeUserPasswordCommand(
                $input->getArgument('email'),
                $input->getArgument('password')
            )
        );

        $output->writeln(sprintf(
            'Changed password of <comment>%s</comment> %s', $input->getArgument('email'), $this->userClass
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
            $question = new Question('Please choose a new password:');
            $question->setValidator(function ($password) {
                if (empty($password)) {
                    throw new \Exception('Password can not be empty');
                }

                return $password;
            });
            $question->setHidden(true);
            $questions['password'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }
}
