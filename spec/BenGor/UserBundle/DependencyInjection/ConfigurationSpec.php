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

namespace spec\BenGor\UserBundle\DependencyInjection;

use BenGor\UserBundle\DependencyInjection\Configuration;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Spec file of configuration class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class ConfigurationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
    }

    function it_implements_symfony_configuration_interface()
    {
        $this->shouldHaveType(ConfigurationInterface::class);
    }

    function it_gets_config_tree_builder()
    {
        $this->getConfigTreeBuilder()->shouldReturnAnInstanceOf(TreeBuilder::class);
    }
}
