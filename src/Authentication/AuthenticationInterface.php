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
     * @return AuthenticationRepresentationInterface
     */
    public function authenticate(string $user, string $password): ?AuthenticationRepresentationInterface;

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
