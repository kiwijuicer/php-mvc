<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc;

use KiwiJuicer\Mvc\Http\Request;
use KiwiJuicer\Mvc\Log\Logger;
use KiwiJuicer\Mvc\Exception\ClassNotFoundException;
use Psr\Container\ContainerInterface;

/**
 * Dependency Manager
 *
 * @package Mvc\Mvc;
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class DependencyManager implements ContainerInterface
{
    /**
     * Invokables config
     *
     * @var array
     */
    protected $invokables;

    /**
     * Factories config
     *
     * @var array
     */
    protected $factories;

    /**
     * Managers config
     *
     * @var array
     */
    protected $managers;

    /**
     * Logger config
     *
     * @var array
     */
    protected $log;

    /**
     * Authentications
     *
     * @var array
     */
    protected $authentications;

    /**
     * Dependency Manager
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->authentications = $config['authentications'] ?? [];
        $this->invokables = $config['invokables'] ?? [];
        $this->factories = $config['factories'] ?? [];
        $this->managers = $config['managers'] ?? [];
        $this->log = $config['log'] ?? [];
        $this->setInvokable(Request::class);
    }

    /**
     * Returns requested instance
     *
     * @param string $name
     * @return mixed
     * @throws \KiwiJuicer\Mvc\Exception\ClassNotFoundException
     */
    public function get($name)
    {
        // Factories
        if (array_key_exists($name, $this->factories)) {
            return (new $this->factories[$name]())($this);
        }

        // Managers
        if (array_key_exists($name, $this->managers)) {

            $managerConfig = $this->managers[$name];

            /** @var \KiwiJuicer\Mvc\Manager\AbstractManager $manager */
            $manager = new $managerConfig['manager'](Db\Db::getConnection(Application::getConfig()['db'], $managerConfig['db']));

            $manager->setPrototype(new $managerConfig['entity']());

            return $manager;
        }

        // Invokables
        if (array_key_exists($name, $this->invokables)) {
            return new $this->invokables[$name]();
        }

        // Authentications
        if (array_key_exists($name, $this->authentications)) {
            return $this->get($this->authentications[$name]);
        }

        // Logger
        if (array_key_exists($name, $this->log)) {

            $logger = new Logger();

            foreach ((array)$this->log[$name]['writers'] as $writerFqn => $writerConfig) {

                if (class_exists($writerFqn)) {
                    $logger->addWriter(new $writerFqn($writerConfig));
                }
            }

            return $logger;
        }

        // Session
        if (mb_strtolower($name) === 'session') {
            return $_SESSION;
        }

        throw new ClassNotFoundException('Requested dependency ' . $name . ' not found, are you certain that you provided the configuration?');
    }

    /**
     * Sets a factory
     *
     * @param string $source
     * @param string $target
     */
    public function setFactory(string $source, string $target): void
    {
        $this->factories[$source] = $target;
    }

    /**
     * Sets invokable
     *
     * @param string $name
     */
    public function setInvokable(string $name): void
    {
        $this->invokables[$name] = $name;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $name Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($name): bool
    {
        try {
            $this->get($name);
            return true;
        } catch (ClassNotFoundException $e) {
            return false;
        }
    }
}
