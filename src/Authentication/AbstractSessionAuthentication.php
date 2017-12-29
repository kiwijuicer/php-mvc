<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Authentication;

/**
 * Abstract Session Authentication
 *
 * @package KiwiJuicer\Mvc\Authentication
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
abstract class AbstractSessionAuthentication implements AuthenticationInterface
{
    /**
     * The authentication key
     *
     * @var string
     */
    const AUTHENTICATION_KEY = '_authentication';

    /**
     * The authorisation representation
     *
     * @var \KiwiJuicer\Mvc\Authentication\AuthenticationRepresentationInterface
     */
    protected $authorisationRepresentation;

    /**
     * Checks for authentication
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return array_key_exists(static::AUTHENTICATION_KEY, $_SESSION) && $_SESSION[static::AUTHENTICATION_KEY] instanceof AuthenticationRepresentationInterface;
    }

    /**
     * Sets the auth representation in session
     *
     * @param \KiwiJuicer\Mvc\Authentication\AuthenticationRepresentationInterface $authenticationRepresentation
     * @return void
     */
    public function setAuthorisationRepresentation(AuthenticationRepresentationInterface $authenticationRepresentation): void
    {
        $_SESSION[static::AUTHENTICATION_KEY] = $authenticationRepresentation;
    }

    /**
     * Destroys the session
     *
     * @return void
     */
    public function destroyAuthentication(): void
    {
        unset($_SESSION[static::AUTHENTICATION_KEY]);
    }
}
