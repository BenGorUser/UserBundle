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

namespace BenGorUser\UserBundle\DependencyInjection\Compiler\Application\DataTransformer;

use BenGorUser\User\Application\DataTransformer\UserDTODataTransformer;
use BenGorUser\UserBundle\DependencyInjection\Compiler\Application\ApplicationBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * User DTO data transformer builder.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class UserDTODataTransformerBuilder implements ApplicationBuilder
{
    /**
     * Configuration array.
     *
     * @var array
     */
    protected $configuration;

    /**
     * The container builder.
     *
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerBuilder $container     The container builder
     * @param array            $configuration The configuration tree
     */
    public function __construct(ContainerBuilder $container, array $configuration = [])
    {
        $this->container = $container;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function build($user)
    {
        $this->container->setDefinition(
            'bengor.user.application.data_transformer.' . $user . '_dto',
            new Definition(
                UserDTODataTransformer::class
            )
        )->setPublic(false);

        $this->container->setAlias(
            'bengor_user.' . $user . '_dto_data_transformer',
            'bengor.user.application.data_transformer.' . $user . '_dto'
        );

        return $this->container;
    }
}
