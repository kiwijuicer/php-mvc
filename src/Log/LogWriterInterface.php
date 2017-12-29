<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Log;

/**
 * Writer Interface
 *
 * @package KiwiJuicer\Mvc\Log
 */
interface LogWriterInterface
{
    /**
     * Write log method
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function write(string $level, string $message, array $context): void;
}
