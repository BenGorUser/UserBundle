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

namespace BenGor\UserBundle\Security;

/**
 * Authenticator service that it is used
 * as a bridge between BenGorUser library's use cases
 * and this bundle's authenticator strategies.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
interface AuthenticatorService
{
    /**
     * Executes method.
     *
     * @param mixed $request The request
     *
     * @return mixed
     */
    public function execute($request = null);
}
