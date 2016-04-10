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

namespace BenGor\UserBundle\DependencyInjection\Compiler;

use BenGor\User\Application\DataTransformer\UserDTODataTransformer;
use BenGor\User\Application\DataTransformer\UserNoTransformationDataTransformer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Register application data transformers compiler pass.
 *
 * Service declaration via PHP allows more
 * flexibility with customization extend users.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ApplicationDataTransformersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->setDefinition(
            'bengor.user.application.data_transformer.user_dto',
            new Definition(
                UserDTODataTransformer::class
            )
        );

        $container->setDefinition(
            'bengor.user.application.data_transformer.user_no_transformation',
            new Definition(
                UserNoTransformationDataTransformer::class
            )
        );
    }
}
