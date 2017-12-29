<?php
declare(strict_types = 1);

namespace KiwiJuicer\Mvc\Exception;

use KiwiJuicer\Mvc\Application;
use KiwiJuicer\Mvc\View\View;
use Psr\Log\LoggerInterface;

/**
 * Error/exception handling
 *
 * @package KiwiJuicer\Mvc\Exception
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class Handler
{
    /**
     * Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    public static function setErrorHandler(LoggerInterface $logger): void
    {
        set_error_handler(function ($level, $message, $file, $line) use ($logger) {
            if (error_reporting() !== 0) {
                Handler::handle(new \ErrorException($message, 0, $level, $file, $line), $logger);
            }
        });
    }

    /**
     * Exception handle.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     *
     */
    public static function setExceptionHandler(LoggerInterface $logger): void
    {
        set_exception_handler(function(\Throwable $exception) use ($logger) {
            Handler::handle($exception, $logger);
        });
    }

    /**
     * The generic error/exception handle
     *
     * @param \Throwable $exception
     * @param \Psr\Log\LoggerInterface $logger
     * @return void
     */
    protected static function handle(\Throwable $exception, LoggerInterface $logger): void
    {

        $code = $exception->getCode();

        if ($code !== 404) {
            $code = 500;
        }

        http_response_code($code);

        $environment = Application::getConfig()['env'];

        $logger->emergency($exception);

        View::render('error/' . $code . '.phtml', [
            'exception' => $environment === 'development' ? $exception : null
        ]);

        exit();
    }
}
