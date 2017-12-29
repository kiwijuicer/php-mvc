<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Authentication;

/**
 * Authentication Manager
 *
 * @package KiwiJuicer\Mvc\Authentication
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class AuthenticationManager implements AuthenticationInterface
{
    /**
     * Authenticator stack
     *
     * @var AuthenticationInterface[]
     */
    protected $authenticationStack = [];

    /**
     * The winning authenticator
     *
     * @var \KiwiJuicer\Mvc\Authentication\AuthenticationInterface
     */
    protected $authenticator;

    /**
     * Authenticates
     *
     * @param string $user
     * @param string $password
     * @return \KiwiJuicer\Mvc\Authentication\AuthenticationRepresentationInterface|null
     */
    public function authenticate(string $user, string $password): ?AuthenticationRepresentationInterface
    {
        foreach ($this->authenticationStack as $authenticator) {

            $authenticationRepresentation = $authenticator->authenticate($user, $password);

            if ($authenticationRepresentation instanceof AuthenticationRepresentationInterface) {
                $this->authenticator = $authenticator;
                return $authenticationRepresentation;
            }
        }

        return null;
    }

    /**
     * Returns if is authenticated or not
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        foreach ($this->authenticationStack as $authenticator) {
            if ($authenticator->isAuthenticated()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds given authenticator to stack
     *
     * @param \KiwiJuicer\Mvc\Authentication\AuthenticationInterface $authenticator
     */
    public function addAuthenticator(AuthenticationInterface $authenticator)
    {
        $this->authenticationStack[] = $authenticator;
    }

    /**
     * Destroys the authentication of all authenticators
     *
     * @return void
     */
    public function destroyAuthentication(): void
    {
        foreach ($this->authenticationStack as $authenticator) {
            $authenticator->destroyAuthentication();
        }
    }
}
