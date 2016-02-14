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

use BenGor\User\Application\Service\ActivateUserAccountRequest;
use Ddd\Application\Service\TransactionalApplicationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Activate user account command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ActivateUserAccountCommand extends Command
{
    /**
     * The activate user account service.
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
     * @param TransactionalApplicationService $service   The activate user account service
     * @param string                          $userClass The user class
     */
    public function __construct(TransactionalApplicationService $service, $userClass)
    {
        parent::__construct();

        $this->service = $service;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('bengor:user:enable')
            ->setDescription('Activate a user account.')
            ->setDefinition([
                new InputArgument(
                    'confirmation-token',
                    InputArgument::REQUIRED,
                    'The confirmation token'
                ),
            ])
            ->setHelp(<<<EOT
The <info>bengor:user:enable</info> command enables a user:

  <info>php bin/console bengor:user:enable confirmation-token</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->execute(
            new ActivateUserAccountRequest(
                $input->getArgument('confirmation-token')
            )
        );

        $output->writeln(sprintf('Enabled <comment>%s</comment>', $this->userClass));
    }
}
