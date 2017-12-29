<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Authentication;

/**
 * Dummy Authentication
 *
 * @package KiwiJuicer\Mvc\Authentication
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class DummyAuthentication implements AuthenticationInterface
{
    /**
     * Checks for authentication
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return true;
    }

    /**
     * Authenticate by given email and password
     *
     * @param string $email
     * @param string $password
     * @return AuthenticationRepresentationInterface
     */
    public function authenticate(string $email, string $password): AuthenticationRepresentationInterface
    {
        return true;
    }

    /**
     * Destroys the session
     *
     * @return void
     */
    public function destroyAuthentication(): void
    {
        // Nothing to do
    }
}
