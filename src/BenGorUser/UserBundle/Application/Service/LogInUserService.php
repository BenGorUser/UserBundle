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

namespace BenGorUser\UserBundle\Application\Service;

use BenGorUser\User\Application\Service\LogIn\LogInUserService as BaseLogInUserService;
use BenGorUser\UserBundle\Security\AuthenticatorService;

/**
 * Decorated BenGorUser's library LogInUserService class.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class LogInUserService extends BaseLogInUserService implements AuthenticatorService
{
}
