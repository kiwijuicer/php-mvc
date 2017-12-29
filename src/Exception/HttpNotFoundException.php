<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Exception;

/**
 * Http Not Found Exception
 *
 * @package KiwiJuicer\Mvc\Exception
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class HttpNotFoundException extends \Exception
{
    /**
     * Http Not Found Exception Constructor
     *
     * @param string $message
     * @param int|null $code
     */
    public function __construct(string $message = null, int $code = null)
    {
        parent::__construct($message ?? '404 - File not found', $code ?? 404);
    }
}
