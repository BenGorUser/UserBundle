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

use BenGor\User\Application\Service\EnableUserRequest;
use Ddd\Application\Service\TransactionalApplicationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Enable user account command.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class EnableUserCommand extends Command
{
    /**
     * The enable user service.
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
        $this->service = $service;
        $this->userClass = $userClass;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(sprintf('bengor:user:%s:enable', $this->userClass))
            ->setDescription(sprintf('Enable a %s account.', $this->userClass))
            ->setDefinition([
                new InputArgument(
                    'confirmation-token',
                    InputArgument::REQUIRED,
                    'The confirmation token'
                ),
            ])
            ->setHelp(<<<EOT
The <info>bengor:user:$this->userClass:enable</info> command enables a $this->userClass:

  <info>php bin/console bengor:user:$this->userClass:enable confirmation-token</info>

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->execute(
            new EnableUserRequest(
                $input->getArgument('confirmation-token')
            )
        );

        $output->writeln(sprintf('Enabled <comment>%s</comment>', $this->userClass));
    }
}
