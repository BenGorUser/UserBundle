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

namespace BenGorUser\UserBundle\Form\Type;

use BenGorUser\User\Application\Command\SignUp\ByInvitationWithConfirmationSignUpUserCommand;

/**
 * By invitation with confirmation sign up user form type.
 *
 * @author Beñat Espiña <benatespina@gmail.com>
 */
class SignUpByInvitationWithConfirmationType extends SignUpByInvitationType
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->command = ByInvitationWithConfirmationSignUpUserCommand::class;
    }
}
