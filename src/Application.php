<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc;

use KiwiJuicer\Mvc\Authentication\AuthenticationInterface;
use KiwiJuicer\Mvc\Authentication\AuthenticationManager;
use KiwiJuicer\Mvc\Authentication\DummyAuthentication;
use KiwiJuicer\Mvc\Authentication\SessionAuthentication;
use KiwiJuicer\Mvc\Authentication\UserAuthentication;
use KiwiJuicer\Mvc\Exception\Handler;
use KiwiJuicer\Mvc\Http\Request;
use KiwiJuicer\Mvc\Log\Logger;
use KiwiJuicer\Mvc\Routing\Router;

/**
 * Application
 *
 * @package KiwiJuicer\Mvc
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class Application
{
    /**
     * Config
     *
     * @var array
     */
    protected static $config;

    /**
     * Dependency Manager
     *
     * @var \KiwiJuicer\Mvc\DependencyManager
     */
    protected static $dependencyManager;

    /**
     * Inits application with dependency manager and returns instance of itself
     *
     * @param array $configs
     * @return \KiwiJuicer\Mvc\Application
     */
    public static function init(array $configs): self
    {
        session_start();

        self::$config = array_merge(...$configs);

        self::$dependencyManager = new DependencyManager(self::$config['dependencies'] ?? []);

        Handler::setErrorHandler(self::$dependencyManager->get(Logger::LOGGER_PHP));
        Handler::setExceptionHandler(self::$dependencyManager->get(Logger::LOGGER_EXCEPTION));

        return new self();
    }

    /**
     * Returns dependency manager
     *
     * @return \KiwiJuicer\Mvc\DependencyManager
     */
    public static function getDependencyManager(): DependencyManager
    {
        return self::$dependencyManager;
    }

    /**
     * Returns config
     *
     * @return array
     */
    public static function getConfig(): array
    {
        return self::$config;
    }

    /**
     * Returns authentication manager
     *
     * @param array|null $config
     * @return \KiwiJuicer\Mvc\Authentication\AuthenticationManager
     */
    protected function getAuthenticationManager(array $config = null): AuthenticationManager
    {
        $authenticationManager = new AuthenticationManager();

        if ($config !== null && array_key_exists('classes', $config)) {
            foreach ((array)$config['classes'] as $authenticatorFqn) {
                $authenticationManager->addAuthenticator(self::getDependencyManager()->get($authenticatorFqn));
            }
        } else {
            $authenticationManager->addAuthenticator(new DummyAuthentication());
        }

        return $authenticationManager;
    }

    /**
     * Runs the application
     *
     * @var void
     */
    public function run(): void
    {
        $route = Router::match(self::getDependencyManager()->get(Request::class));

        $router = new Router($this->getAuthenticationManager(self::getConfig()['authentication'] ?? null));

        $router->routeTo($route);
    }
}
