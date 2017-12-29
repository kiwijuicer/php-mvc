<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Authentication;

/**
 * Authentication Interface
 *
 * @package KiwiJuicer\Mvc\Authentication
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
interface AuthenticationInterface
{
    /**
     * Authenticates and returns whether it succeeded or not
     *
     * @param string $user
     * @param string $password
     * @return bool
     */
    public function authenticate(string $user, string $password): bool;

    /**
     * Destroys the authentication
     *
     * @return void
     */
    public function destroyAuthentication(): void;

    /**
     * Tells if authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool;
}
