<?php

namespace Extractor\Connection;

use PDO;

class PdoConnection implements ConnectionInterface
{
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function query(string $command, array $arguments)
    {
        $stmt = $this->pdo->prepare($command);
        $stmt->execute($arguments);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }
}
