<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Authentication;

/**
 * Interface AuthenticationInterface
 *
 * @package KiwiJuicer\Mvc\Authentication
 */
interface AuthenticatorInterface
{
    /**
     * Authenticates
     *
     * @param string $user
     * @param string $password
     * @return \KiwiJuicer\Mvc\Authentication\AuthenticationRepresentationInterface
     */
    public function authenticate(string $user, string $password): AuthenticationRepresentationInterface;
}
