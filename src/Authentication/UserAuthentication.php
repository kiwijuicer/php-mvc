<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Authentication;

/**
 * User Authentication
 *
 * @package KiwiJuicer\Mvc\Authentication
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class UserAuthentication implements AuthenticationInterface
{
    /**
     * @var \KiwiJuicer\Mvc\Authentication\AuthenticatorInterface
     */
    protected $authenticator;

    /**
     * User Authentication Constructor
     *
     * @param \KiwiJuicer\Mvc\Authentication\AuthenticatorInterface $authenticator
     */
    public function __construct(AuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Checks for authentication
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return array_key_exists('user', $_SESSION) && $_SESSION['user'] instanceof AuthenticationRepresentationInterface;
    }

    /**
     * Authenticate by given email and password
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authenticate(string $email, string $password): bool
    {
        $authRepresentation = $this->authenticator->authenticate($email, $password);

        if ($authRepresentation instanceof AuthenticationRepresentationInterface) {
            $_SESSION['user'] = $authRepresentation;
            return true;
        }

        return false;
    }

    /**
     * Destroys the session
     *
     * @return void
     */
    public function destroyAuthentication(): void
    {
        unset($_SESSION['user']);
    }
}
