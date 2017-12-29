<?php
declare (strict_types = 1);

namespace KiwiJuicer\Mvc\Manager;

use KiwiJuicer\Mvc\Entity\AbstractEntity;

/**
 * Abstract Manager
 *
 * @package KiwiJuicer\Mvc\Manager
 * @author Norbert Hanauer <info@norbert-hanauer.de>
 */
abstract class AbstractManager
{
    /**
     * Table name
     *
     * @var string
     */
    const TABLE_NAME = '';

    /**
     * Table gateway
     *
     * @var \PDO
     */
    protected $tableGateway;

    /**
     * Prototype
     *
     * @var AbstractEntity
     */
    protected $prototype;

    /**
     * AbstractManager Constructor
     *
     * @param \PDO $tableGateway
     */
    public function __construct(\PDO $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Sets the entity prototype
     * @param \KiwiJuicer\Mvc\Entity\AbstractEntity $entity
     * @return void
     */
    public function setPrototype(AbstractEntity $entity): void
    {
        $this->prototype = $entity;
    }

    /**
     * Returns entity by primary key
     *
     * @param int $primaryKey
     * @return AbstractEntity
     */
    public function get(int $primaryKey): AbstractEntity
    {
        $sql = 'SELECT
                    *
                FROM
                    ' . static::TABLE_NAME . '
                WHERE
                    ' . $this->prototype->primaryKey . ' = ' . $primaryKey;

        $result = $this->tableGateway->query($sql)->fetch(\PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            return $this->create($result);
        }

        return null;
    }

    /**
     * Creates entity and hydrates when data is given
     *
     * @param array|null $data
     * @return AbstractEntity
     */
    public function create(array $data = null): AbstractEntity
    {
        $entity = clone $this->prototype;

        if ($data !== null) {
            foreach ($data as $field => $value) {
                if (array_key_exists($field, $entity->fields)) {
                    $entity->prefixSet(str_replace($entity->prefix, '', $field), $value);
                }
            }
        }

        return $entity;
    }

    /**
     * Saves entity
     *
     * @param AbstractEntity $entity
     * @return int
     * @throws \RuntimeException
     */
    public function save(AbstractEntity $entity): int
    {
        $bindings = [];
        $params = [];
        $fields = $entity->fields;

        unset($fields[$entity->primaryKey]);

        // Update

        if ($entity->isPersisted()) {

            $sets = [];

            foreach ($fields as $field => $value) {
                $param = ':' . $field;
                $bindings[$param] = $value;
                $sets[] = $field . ' = ' . $param;
            }

            $sql = 'UPDATE ' . static::TABLE_NAME . ' SET ' . implode(', ', $sets) . ' WHERE ' . $this->prototype->primaryKey . ' = ' . $entity->getId();

            $pdoStatement = $this->tableGateway->prepare($sql);

            foreach($bindings as $param => $value){
                $pdoStatement->bindValue($param, $value);
            }

            $result = $pdoStatement->execute();

            if (!$result) {
                throw new \RuntimeException('DB update failed with error: ' . ($pdoStatement->errorInfo()[2] ?? '"unknown"') . ' and code ' . $pdoStatement->errorCode());
            }

            return $entity->getId();
        }

        // Insert

        foreach($fields as $field => $value){
            $param = ':' . $field;
            $params[] = $param;
            $bindings[$param] = $value;
        }

        $sql = 'INSERT INTO ' . static::TABLE_NAME . ' (' . implode(', ', array_keys($fields)) . ') VALUES (' . implode(', ', $params) . ')';

        $pdoStatement = $this->tableGateway->prepare($sql);

        foreach($bindings as $param => $value){
            $pdoStatement->bindValue($param, $value);
        }

        $result = $pdoStatement->execute();

        if (!$result) {
            throw new \RuntimeException('DB insert failed with error: ' . ($pdoStatement->errorInfo()[2] ?? '"unknown"') . ' and code ' . $pdoStatement->errorCode());
        }

        return (int)$this->tableGateway->lastInsertId($entity->primaryKey);
    }

    /**
     * Returns all entities
     *
     * @return AbstractEntity[]
     */
    public function fetchAll(): array
    {
        $entities = [];

        $sql = 'SELECT * FROM ' . static::TABLE_NAME;

        $results = $this->tableGateway->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $entity = $this->create($result);
            $entities[$entity->getId()] = $entity;
        }

        return $entities;
    }
}
