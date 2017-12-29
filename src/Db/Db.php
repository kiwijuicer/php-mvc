<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Db;

/**
 * Db
 *
 * @package KiwiJuicer\Mvc\Db;
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
class Db
{
    /**
     * Set of persisted connections
     *
     * @var \PDO[]
     */
    static protected $connectionPool = [];

    /**
     * Returns db connection persisted
     *
     * @param $config
     * @param $dbName
     * @return \PDO
     * @throws \InvalidArgumentException
     */
    public static function getConnection($config, $dbName): \PDO
    {
        if (!array_key_exists($dbName, $config)) {
            throw new \InvalidArgumentException('Request db ' . $dbName . ' not found in config');
        }

        if (array_key_exists($dbName, self::$connectionPool)) {
            return self::$connectionPool[$dbName];
        }

        $dbConfig = $config[$dbName];

        return new \PDO($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password'], $dbConfig['driver_options'] ?? []);
    }
}
