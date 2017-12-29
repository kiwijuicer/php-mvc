<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Controller;

use KiwiJuicer\Mvc\Authentication\AuthenticationInterface;
use KiwiJuicer\Mvc\Http\Request;

/**
 * Abstract Controller
 */
abstract class AbstractController
{
    /**
     * Request
     *
     * @var \KiwiJuicer\Mvc\Http\Request
     */
    protected $request;

    /**
     * Authentication
     *
     * @var \KiwiJuicer\Mvc\Authentication\AuthenticationInterface|null
     */
    protected $authentication;

    /**
     * Sets authentication
     *
     * @param \KiwiJuicer\Mvc\Authentication\AuthenticationInterface $authentication
     */
    public function setAuthentication(AuthenticationInterface $authentication = null): void
    {
        $this->authentication = $authentication;
    }

    /**
     * Returns authentication
     *
     * @return \KiwiJuicer\Mvc\Authentication\AuthenticationInterface
     */
    public function getAuthentication(): ?AuthenticationInterface
    {
        return $this->authentication;
    }

    /**
     * Sets the request
     *
     * @param \KiwiJuicer\Mvc\Http\Request $request
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Returns the request
     *
     * @return \KiwiJuicer\Mvc\Http\Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
