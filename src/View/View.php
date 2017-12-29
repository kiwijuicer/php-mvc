<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\View;

use KiwiJuicer\Mvc\Application;

/**
 * View
 *
 * @package KiwiJuicer\Mvc\View;
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class View
{
    /**
     * Template path
     *
     * @var string
     */
    protected $templatePath;

    /**
     * Inject
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Layout
     *
     * @var string
     */
    protected $layoutPath;

    /**
     * String to the public base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * Content
     *
     * @var string
     */
    protected $content;

    /**
     * View Constructor
     *
     * @param string $templatePath
     * @param array|null $injectVariables
     * @throws \InvalidArgumentException
     */
    public function __construct(string $templatePath, array $injectVariables = null)
    {
        $config = Application::getConfig();

        if (array_key_exists('view-manager', $config)) {

            // Inject variables configured in the view manager config
            if (array_key_exists('public', $config['view-manager'])) {
                $this->basePath = $config['view-manager']['public'];
            } else {
                $this->basePath = getcwd();
            }

            // Inject variables configured in the view manager config
            if (array_key_exists('inject', $config['view-manager'])) {
                $this->variables = $config['view-manager']['inject'];
            }

            // Check templates for a layout path
            if (array_key_exists('layout', $config['view-manager'])) {

                $layoutPath = $config['view-manager']['layout'];

                if (!file_exists($layoutPath)) {
                    throw new \InvalidArgumentException('Configured layout could not be found: ' . $layoutPath);
                }

                $this->layoutPath = $layoutPath;
            }

            // First check the path stack for matching paths
            if (array_key_exists('path-stack', $config['view-manager'])) {

                foreach ((array)$config['view-manager']['path-stack'] as $pathName => $path) {
                    if (file_exists($path . '/' . $templatePath)) {
                        $this->templatePath = $path . '/' . $templatePath;
                        break;
                    }
                }
            }

            // If there is a matching error page use it (instead)
            if ($this->templatePath === null && array_key_exists('templates', $config['view-manager'])) {

                foreach ((array)$config['view-manager']['templates'] as $pathName => $path) {
                    if (file_exists($path . '/' . $templatePath)) {
                        $this->templatePath = $path . '/' . $templatePath;
                        break;
                    }
                }
            }
        } else {
            if (file_exists($templatePath)) {
                $this->templatePath = $templatePath;
            }
        }

        if ($injectVariables !== null) {

            foreach ($injectVariables as $name => $value) {
                $this->variables[$name] = $value;
            }
        }

        foreach ($this->variables as $name => $value) {
            $this->{$name} = $value;
        }

        if ($this->templatePath === null) {
            throw new \InvalidArgumentException('Template path name could not be resolved: ' . $templatePath);
        }

        ob_start();
        include $this->templatePath;
        $this->content = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Returns the template path
     *
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Instantiated render method
     *
     * @return void
     */
    public function show(): void
    {
        if ($this->layoutPath !== null) {
            include $this->layoutPath;
        } else {
            include $this->templatePath;
        }
    }

    /**
     * Static render method
     *
     * @param string $templatePath
     * @param array|null $variables
     * @return void
     */
    public static function render(string $templatePath, array $variables = null): void
    {
        (new self($templatePath, $variables))->show();
    }
}
