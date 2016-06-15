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
use Symfony\Component\DependencyInjection\Reference;

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
     * The FQCN or service id of user data transformer.
     *
     * @var string
     */
    protected $dataTransformer;

    /**
     * Constructor.
     *
     * @param ContainerBuilder $container       The container builder
     * @param string           $dataTransformer The FQCN of user data transformer
     * @param array            $configuration   The configuration tree
     */
    public function __construct(ContainerBuilder $container, $dataTransformer, array $configuration = [])
    {
        $this->container = $container;
        $this->dataTransformer = $dataTransformer;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function build($user)
    {
        $dataTransformer = class_exists($this->dataTransformer)
            ? new Definition($this->dataTransformer)
            : new Reference($this->dataTransformer);

        $this->container->setDefinition(
            'bengor.user.application.data_transformer.' . $user . '_dto',
            $dataTransformer
        )->setPublic(false);

        $this->container->setAlias(
            'bengor_user.' . $user . '.dto_data_transformer',
            'bengor.user.application.data_transformer.' . $user . '_dto'
        );

        return $this->container;
    }
}
