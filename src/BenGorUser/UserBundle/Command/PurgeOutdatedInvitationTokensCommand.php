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

use BenGorUser\User\Application\Command\PurgeOutdatedTokens\PurgeOutdatedInvitationTokensUserCommand;
use BenGorUser\User\Infrastructure\CommandBus\UserCommandBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Purge outdated invitation tokens command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class PurgeOutdatedInvitationTokensCommand extends Command
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
            ->setName(sprintf('bengor:user:%s:purge-outdated-invitation-tokens', $this->userClass))
            ->setDescription(sprintf('Purge outdated invitation tokens of a %s.', $this->userClass))
            ->setHelp(<<<EOT
The <info>bengor:user:$this->userClass:purge-outdated-invitation-tokens</info> command purges outdated invitation tokens of a $this->userClass:

  <info>php bin/console bengor:user:$this->userClass:purge-outdated-invitation-tokens</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandBus->handle(
            new PurgeOutdatedInvitationTokensUserCommand()
        );

        $output->writeln(sprintf('Purged outdated invitation tokens of %s', $this->userClass));
    }
}
