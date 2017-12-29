<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Routing;

use KiwiJuicer\Mvc\Application;
use KiwiJuicer\Mvc\Authentication\AuthenticationInterface;
use KiwiJuicer\Mvc\Exception\HttpNotFoundException;
use KiwiJuicer\Mvc\Http\Request;
use KiwiJuicer\Mvc\View\View;

/**
 * Router
 *
 * @package KiwiJuicer\Mvc\Routing
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class Router
{
    /**
     * Template
     *
     * @var string
     */
    protected $template;

    /**
     * Authentication (optional)
     *
     * @var \KiwiJuicer\Mvc\Authentication\AuthenticationInterface
     */
    protected $authentication;

    /**
     * Router Constructor
     *
     * @param \KiwiJuicer\Mvc\Authentication\AuthenticationInterface|null $authentication
     */
    public function __construct(AuthenticationInterface $authentication = null)
    {
        $this->authentication = $authentication;
    }

    /**
     * Matches and returns route against request
     *
     * @param \KiwiJuicer\Mvc\Http\Request $request
     * @return \KiwiJuicer\Mvc\Routing\Route
     * @throws \KiwiJuicer\Mvc\Exception\HttpNotFoundException
     */
    public static function match(Request $request): Route
    {
        $routes = Application::getConfig()['routes'] ?? [];

        foreach ($routes as $routeConfig) {

            $route = new Route($routeConfig);
            $path = str_replace('?' . $request->getServer()['QUERY_STRING'], '', $request->getServer()['REQUEST_URI']);

            if ($route->getPath() === $path) {
                return $route;
            }
        }

        throw new HttpNotFoundException('Given request did not match any route');
    }

    /**
     * Routes to given route
     *
     * @param \KiwiJuicer\Mvc\Routing\Route $route
     * @return void
     */
    public function routeTo(Route $route): void
    {
        // Check for authentication
        if ($this->authentication !== null && $route->needsAuthentication() && !$this->authentication->isAuthenticated()) {
            self::redirect('/login', [
                'old-uri' => $route->getPath()
            ]);
        }

        /** @var \KiwiJuicer\Mvc\Controller\AbstractController $controller */
        $controller = Application::getDependencyManager()->get($route->getControllerName());

        $controller->setRequest(Application::getDependencyManager()->get(Request::class));
        $controller->setAuthentication($this->authentication);

        $explodedAction = explode('-', $route->getActionName());

        $actionName = '';

        foreach ($explodedAction as $actionPart) {
            $actionName .= ($actionName === '') ? $actionPart : ucfirst($actionPart);
        }

        $actionName .= 'Action';

        $viewParams = $controller->{$actionName}();

        if ($this->template !== null) {
            $template = $this->template;
        } else {
            $explodedControllerName = explode('\\', $route->getControllerName());
            $directory = mb_strtolower(str_replace('Controller', '', end($explodedControllerName)));
            $template = $directory . '/' . $route->getActionName() . '.phtml';
        }

        $view = new View($template, $viewParams);

        $view->show();
    }

    /**
     * Overrides the template
     *
     * @param string $template
     * @return void
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * Redirects to url
     *
     * @param string $uri
     * @param array|null $params
     * @return void
     */
    public static function redirect(string $uri, array $params = null): void
    {
        $url = Application::getConfig()['base-url'] . $uri . ($params !== null ? '?' . http_build_query($params) : '');
        header('Location: ' . $url);
        exit();
    }
}
