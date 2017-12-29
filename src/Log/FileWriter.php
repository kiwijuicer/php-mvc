<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Log;

/**
 * Psr File Logger
 *
 * @package KiwiJuicer\Mvc\Log
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class FileWriter implements LogWriterInterface
{
    /**
     * Log file path
     *
     * @var string
     */
    protected $filePath;

    /**
     * File Logger Constructor
     *
     * @param array $config
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config)
    {
        if (!array_key_exists('path', $config)) {
            throw new \InvalidArgumentException('Given file writer config needs a "path" configuration');
        }

        $this->filePath = $config['path'];

        if (!file_exists($this->filePath)){
            $this->filePath = getcwd() . '/' . $this->filePath;
        }
    }

    /**
     * Write log method
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function write(string $level, string $message, array $context): void
    {
        $logMessage = [];

        $logMessage[] = date('Y-m-d H:i:s') . ' - ' . ucfirst($level);

        if (array_key_exists('exception', $context)) {
            $logMessage[] = get_class($context['exception']);
        }

        $logMessage[] = 'with message: "' . $message;

        if (array_key_exists('trace', $context)) {
            $logMessage[] = 'Stack trace: ' . $context['trace'];
        }

        if (array_key_exists('file', $context) && array_key_exists('line', $context)) {
            $logMessage[] = 'Thrown in "' . $context['file'] . '" on line ' . $context['line'];
        }

        $logMessageString = implode("\n", $logMessage) . "\n";

        file_put_contents($this->filePath, $logMessageString, FILE_APPEND);
    }
}
